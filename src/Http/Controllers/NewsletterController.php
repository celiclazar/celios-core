<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Jobs\Newsletter\SendSubscriptionConfirmationJob;
use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterCampaignLog;
use Celios\Core\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Handle public subscription request.
     */
    public function subscribe(Request $request, ?string $locale = null): JsonResponse|RedirectResponse
    {
        $supportedLocales = ['sr', 'en', 'it'];
        $currentLocale = ($locale && in_array($locale, $supportedLocales))
            ? $locale
            : app()->getLocale();

        App::setLocale($currentLocale);

        // 1. Spam Honeypot verification (matching FormSubmissionController)
        if ($request->filled('_hp_name')) {
            return $this->subscribeResponse($request, true, __('newsletter.subscribed_success'));
        }

        if ($request->filled('_hp_time')) {
            $renderTime = (int) $request->input('_hp_time');
            if (time() - $renderTime < 2) {
                return $this->subscribeResponse($request, true, __('newsletter.subscribed_success'));
            }
        }

        // 2. Validate input
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
        ]);

        $email = strtolower(trim($validated['email']));
        $doubleOptIn = (bool) setting('newsletter_double_opt_in', true);

        // 3. Find or initialize subscriber
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'active') {
                return $this->subscribeResponse($request, true, __('newsletter.already_subscribed'));
            }

            // If pending or unsubscribed, re-issue confirmation or reactivate
            $subscriber->update([
                'first_name' => $validated['first_name'] ?? $subscriber->first_name,
                'last_name' => $validated['last_name'] ?? $subscriber->last_name,
                'locale' => $currentLocale,
                'ip_address' => $request->ip(),
                'status' => $doubleOptIn ? 'pending' : 'active',
                'verification_token' => $doubleOptIn ? Str::random(40) : null,
                'verified_at' => $doubleOptIn ? null : now(),
            ]);
        } else {
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'locale' => $currentLocale,
                'status' => $doubleOptIn ? 'pending' : 'active',
                'verification_token' => $doubleOptIn ? Str::random(40) : null,
                'verified_at' => $doubleOptIn ? null : now(),
                'unsubscribe_token' => Str::random(40),
                'ip_address' => $request->ip(),
                'signup_source' => $request->input('source', 'website'),
            ]);
        }

        // 4. Dispatch verification job if double opt-in is active
        if ($doubleOptIn && $subscriber->status === 'pending') {
            SendSubscriptionConfirmationJob::dispatch($subscriber);
            return $this->subscribeResponse($request, true, __('newsletter.confirm_email_sent'));
        }

        return $this->subscribeResponse($request, true, __('newsletter.subscribed_success'));
    }

    /**
     * Double Opt-In verification page.
     */
    public function verify(Request $request, ?string $locale = null, ?string $token = null): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if ($token === null && $locale !== null) {
            $token = $locale;
            $locale = null;
        }

        if ($locale && in_array($locale, ['sr', 'en', 'it'])) {
            App::setLocale($locale);
        }

        $subscriber = NewsletterSubscriber::where('verification_token', $token)->first();

        if (!$subscriber) {
            // Check if already verified
            $alreadyActive = NewsletterSubscriber::where('status', 'active')->whereNull('verification_token')->exists();
            return view('newsletter.verified', [
                'status' => 'already_or_invalid',
                'message' => __('newsletter.verify_invalid_or_expired'),
            ]);
        }

        $subscriber->markVerified();

        return view('newsletter.verified', [
            'status' => 'success',
            'subscriber' => $subscriber,
            'message' => __('newsletter.verify_success_message'),
        ]);
    }

    /**
     * Unsubscribe web page.
     */
    public function unsubscribe(Request $request, ?string $locale = null, ?string $token = null): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if ($token === null && $locale !== null) {
            $token = $locale;
            $locale = null;
        }

        if ($locale && in_array($locale, ['sr', 'en', 'it'])) {
            App::setLocale($locale);
        }

        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404);
        }

        $subscriber->markUnsubscribed();

        return view('newsletter.unsubscribed', [
            'subscriber' => $subscriber,
            'message' => __('newsletter.unsubscribed_success'),
        ]);
    }

    /**
     * RFC 8058 One-Click Unsubscribe POST endpoint.
     */
    public function unsubscribeRfc(string $token): Response
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if ($subscriber) {
            $subscriber->markUnsubscribed();
        }

        return response('Unsubscribed successfully', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Track email opens via 1x1 transparent GIF pixel.
     */
    public function trackOpen(string $token): Response
    {
        $log = NewsletterCampaignLog::where('tracking_token', $token)->first();

        if ($log && is_null($log->opened_at)) {
            $log->update([
                'opened_at' => now(),
            ]);

            NewsletterCampaign::where('id', $log->campaign_id)->increment('open_count');
        }

        // 1x1 transparent GIF
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($gif, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Track link clicks and redirect safely.
     */
    public function trackClick(Request $request, string $token): RedirectResponse
    {
        $target = $request->query('target');

        $log = NewsletterCampaignLog::where('tracking_token', $token)->first();

        if ($log) {
            if (is_null($log->clicked_at)) {
                $log->update(['clicked_at' => now()]);
                NewsletterCampaign::where('id', $log->campaign_id)->increment('click_count');
            }

            if (is_null($log->opened_at)) {
                $log->update(['opened_at' => now()]);
                NewsletterCampaign::where('id', $log->campaign_id)->increment('open_count');
            }
        }

        if (empty($target) || !filter_var($target, FILTER_VALIDATE_URL)) {
            return redirect()->to('/');
        }

        return redirect()->away($target);
    }

    private function subscribeResponse(Request $request, bool $success, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('newsletter_status', [
            'success' => $success,
            'message' => $message,
        ]);
    }
}
