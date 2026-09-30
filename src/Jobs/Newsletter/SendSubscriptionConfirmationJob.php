<?php

namespace Celios\Core\Jobs\Newsletter;

use Celios\Core\Mail\Newsletter\SubscriptionConfirmationMail;
use Celios\Core\Models\NewsletterSubscriber;
use Celios\Core\Services\Newsletter\NewsletterMailerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSubscriptionConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public NewsletterSubscriber $subscriber
    ) {}

    public function handle(NewsletterMailerService $mailerService): void
    {
        if ($this->subscriber->status !== 'pending' || empty($this->subscriber->verification_token)) {
            return;
        }

        try {
            $mailer = $mailerService->getMailer();
            $fromEmail = $mailerService->getFromAddress();
            $fromName = $mailerService->getFromName();

            $mail = new SubscriptionConfirmationMail($this->subscriber, $fromEmail, $fromName);
            $mailer->to($this->subscriber->email)->send($mail);
        } catch (\Throwable $e) {
            Log::error("Failed to send newsletter confirmation email to {$this->subscriber->email}: " . $e->getMessage());
            throw $e;
        }
    }
}
