<?php

namespace Celios\Core\Jobs\Newsletter;

use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterCampaignLog;
use Celios\Core\Models\NewsletterSubscriber;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public NewsletterCampaign $campaign
    ) {}

    public function handle(NewsletterMailerService $mailerService): void
    {
        // 1. Query active subscribers based on target locales
        $query = NewsletterSubscriber::query()->active();

        $targetLocales = $this->campaign->target_locales ?? [];
        if (!empty($targetLocales) && !in_array('all', $targetLocales)) {
            $query->whereIn('locale', $targetLocales);
        }

        $subscribers = $query->get(['id', 'email', 'locale']);

        if ($subscribers->isEmpty()) {
            $this->campaign->update([
                'status' => 'sent',
                'sent_at' => now(),
                'recipients_count' => 0,
            ]);
            return;
        }

        // 2. Prepare jobs and logs with optional throttling delay
        $jobs = [];
        $campaignId = $this->campaign->id;

        foreach ($subscribers as $index => $subscriber) {
            $log = NewsletterCampaignLog::firstOrCreate(
                [
                    'campaign_id' => $campaignId,
                    'subscriber_id' => $subscriber->id,
                ],
                [
                    'status' => 'queued',
                ]
            );

            $job = new SendNewsletterEmailJob($this->campaign, $subscriber->id, $log->id);

            // Apply throttling delay if configured (e.g. for hosting SMTP limits)
            $delaySeconds = $mailerService->calculateDelaySeconds($index);
            if ($delaySeconds > 0) {
                $job->delay(now()->addSeconds($delaySeconds));
            }

            $jobs[] = $job;
        }

        // 3. Dispatch batch
        $campaign = $this->campaign;

        $batch = Bus::batch($jobs)
            ->name("Newsletter Campaign #{$campaign->id}: {$campaign->title}")
            ->allowFailures()
            ->then(function () use ($campaignId) {
                NewsletterCampaign::where('id', $campaignId)->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
            })
            ->catch(function ($batch, \Throwable $e) use ($campaignId) {
                Log::error("Error in newsletter campaign batch #{$campaignId}: " . $e->getMessage());
            })
            ->finally(function () use ($campaignId) {
                $campaign = NewsletterCampaign::find($campaignId);
                if ($campaign && $campaign->status === 'sending') {
                    $campaign->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                }
            })
            ->dispatch();

        // 4. Update campaign state
        $this->campaign->update([
            'status' => 'sending',
            'batch_id' => $batch->id,
            'recipients_count' => count($jobs),
        ]);
    }
}
