<?php

namespace Celios\Core\Jobs\Newsletter;

use Celios\Core\Mail\Newsletter\NewsletterCampaignMail;
use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterCampaignLog;
use Celios\Core\Models\NewsletterSubscriber;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNewsletterEmailJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function __construct(
        public NewsletterCampaign $campaign,
        public int $subscriberId,
        public int $logId
    ) {}

    public function handle(NewsletterMailerService $mailerService): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $subscriber = NewsletterSubscriber::find($this->subscriberId);
        $log = NewsletterCampaignLog::find($this->logId);

        if (!$subscriber || !$log) {
            return;
        }

        if ($subscriber->status !== 'active') {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Subscriber is not active (status: ' . $subscriber->status . ')',
            ]);
            return;
        }

        try {
            $locale = $subscriber->locale ?: config('locales.default', 'sr');

            // 1. Resolve localized subject and content
            $subject = $this->campaign->getTranslation('subject', $locale);
            if (empty($subject)) {
                $subject = $this->campaign->getTranslation('subject', config('locales.default', 'sr'))
                    ?: $this->campaign->title;
            }

            $content = $this->campaign->getTranslation('content', $locale);
            if (empty($content)) {
                $content = $this->campaign->getTranslation('content', config('locales.default', 'sr')) ?: '';
            }

            // 2. Unsubscribe URL
            $unsubscribeUrl = route('newsletter.unsubscribe.localized', [
                'locale' => $locale,
                'token' => $subscriber->unsubscribe_token,
            ]);

            // 3. Tracking URLs
            $trackingPixelUrl = route('newsletter.track.open', [
                'token' => $log->tracking_token,
            ]);

            // 4. Merge tags replacement
            $replacements = [
                '{{first_name}}' => e($subscriber->first_name ?? ''),
                '{{last_name}}' => e($subscriber->last_name ?? ''),
                '{{name}}' => e($subscriber->full_name),
                '{{email}}' => e($subscriber->email),
                '{{unsubscribe_url}}' => $unsubscribeUrl,
            ];
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            $subject = str_replace(array_keys($replacements), array_values($replacements), $subject);

            // 5. Rewrite external links in content for click tracking
            $content = preg_replace_callback('/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/i', function ($matches) use ($log) {
                $url = $matches[1];
                // Do not rewrite anchors, mailto or tracking/unsubscribe links
                if (empty($url) || str_starts_with($url, '#') || str_starts_with($url, 'mailto:') || str_contains($url, '/newsletter/')) {
                    return $matches[0];
                }

                $trackingRedirect = route('newsletter.track.click', [
                    'token' => $log->tracking_token,
                    'target' => $url,
                ]);

                return str_replace($url, $trackingRedirect, $matches[0]);
            }, $content);

            // 6. Send email
            $mailer = $mailerService->getMailer();
            $fromEmail = $mailerService->getFromAddress();
            $fromName = $mailerService->getFromName();

            $mail = new NewsletterCampaignMail(
                $this->campaign,
                $subscriber,
                $subject,
                $content,
                $unsubscribeUrl,
                $trackingPixelUrl,
                $fromEmail,
                $fromName
            );

            $mailer->to($subscriber->email)->send($mail);

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $this->campaign->increment('sent_count');
        } catch (\Throwable $e) {
            Log::error("Failed sending newsletter campaign #{$this->campaign->id} to {$subscriber->email}: " . $e->getMessage());

            $log->update([
                'status' => 'failed',
                'error_message' => substr($e->getMessage(), 0, 1000),
            ]);

            throw $e;
        }
    }
}
