<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Jobs\Newsletter\SendSubscriptionConfirmationJob;
use Celios\Core\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Subscribe email to newsletter.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $currentLocale = app()->getLocale();

        // 1. Spam Honeypot verification
        if ($request->filled('_hp_name')) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing!',
            ]);
        }

        // 2. Validate input
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
        ]);

        $email = strtolower(trim($validated['email']));
        $doubleOptIn = (bool) setting('newsletter_double_opt_in', true);

        // 3. Find or initialize subscriber
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'active') {
                return response()->json([
                    'success' => true,
                    'message' => 'You are already subscribed.',
                ]);
            }

            $subscriber->update([
                'status' => $doubleOptIn ? 'pending' : 'active',
                'first_name' => $validated['first_name'] ?? $subscriber->first_name,
                'last_name' => $validated['last_name'] ?? $subscriber->last_name,
                'locale' => $currentLocale,
                'verification_token' => $doubleOptIn ? Str::random(32) : null,
            ]);
        } else {
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'status' => $doubleOptIn ? 'pending' : 'active',
                'locale' => $currentLocale,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'verification_token' => $doubleOptIn ? Str::random(32) : null,
                'subscribed_at' => $doubleOptIn ? null : now(),
            ]);
        }

        // 4. Send Double Opt-In Email if enabled
        if ($doubleOptIn) {
            dispatch(new SendSubscriptionConfirmationJob($subscriber));
            return response()->json([
                'success' => true,
                'message' => 'Please check your inbox to confirm your subscription.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to the newsletter.',
        ]);
    }
}
