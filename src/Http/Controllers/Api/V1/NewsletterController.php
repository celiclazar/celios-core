<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\NewsletterCampaignResource;
use Celios\Core\Http\Resources\V1\NewsletterSubscriberResource;
use Celios\Core\Jobs\Newsletter\SendSubscriptionConfirmationJob;
use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
                'verification_token' => $doubleOptIn ? Str::random(40) : null,
                'verified_at' => $doubleOptIn ? null : now(),
            ]);
        } else {
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'status' => $doubleOptIn ? 'pending' : 'active',
                'locale' => $currentLocale,
                'ip_address' => $request->ip(),
                'signup_source' => 'api',
                'verification_token' => $doubleOptIn ? Str::random(40) : null,
                'verified_at' => $doubleOptIn ? null : now(),
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

    /**
     * Verify double opt-in subscription token.
     */
    public function verify(string $token): JsonResponse
    {
        $subscriber = NewsletterSubscriber::where('verification_token', $token)->first();

        if (! $subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification token.',
            ], 404);
        }

        $subscriber->markVerified();

        return response()->json([
            'success' => true,
            'message' => 'Your email subscription has been verified.',
        ]);
    }

    /**
     * Unsubscribe from newsletter using unsubscribe token.
     */
    public function unsubscribe(string $token): JsonResponse
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (! $subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid unsubscribe token.',
            ], 404);
        }

        $subscriber->markUnsubscribed();

        return response()->json([
            'success' => true,
            'message' => 'You have been unsubscribed from the newsletter.',
        ]);
    }

    /**
     * List newsletter subscribers (Admin).
     */
    public function subscribers(Request $request): AnonymousResourceCollection
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $subscribers = NewsletterSubscriber::query()
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->when($search, fn ($q, $search) => $q->where(function ($sub) use ($search) {
                $sub->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            }))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return NewsletterSubscriberResource::collection($subscribers);
    }

    /**
     * Delete a newsletter subscriber (Admin).
     */
    public function destroySubscriber(NewsletterSubscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json([
            'message' => 'Subscriber deleted successfully.',
        ]);
    }

    /**
     * List newsletter campaigns (Admin).
     */
    public function campaigns(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $campaigns = NewsletterCampaign::query()
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return NewsletterCampaignResource::collection($campaigns);
    }

    /**
     * Create a new newsletter campaign (Admin).
     */
    public function storeCampaign(Request $request): NewsletterCampaignResource
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'array'],
            'preview_text' => ['nullable', 'array'],
            'content' => ['required', 'array'],
            'target_locales' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'in:draft,scheduled'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $validated['status'] = $validated['status'] ?? 'draft';

        $campaign = NewsletterCampaign::create($validated);

        return new NewsletterCampaignResource($campaign);
    }

    /**
     * Show a newsletter campaign (Admin).
     */
    public function showCampaign(NewsletterCampaign $campaign): NewsletterCampaignResource
    {
        return new NewsletterCampaignResource($campaign);
    }

    /**
     * Update a newsletter campaign (Admin).
     */
    public function updateCampaign(Request $request, NewsletterCampaign $campaign): NewsletterCampaignResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'subject' => ['sometimes', 'required', 'array'],
            'preview_text' => ['nullable', 'array'],
            'content' => ['sometimes', 'required', 'array'],
            'target_locales' => ['nullable', 'array'],
            'status' => ['nullable', 'string', 'in:draft,scheduled'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $campaign->update($validated);

        return new NewsletterCampaignResource($campaign);
    }

    /**
     * Delete a newsletter campaign (Admin).
     */
    public function destroyCampaign(NewsletterCampaign $campaign): JsonResponse
    {
        $campaign->delete();

        return response()->json([
            'message' => 'Campaign deleted successfully.',
        ]);
    }
}
