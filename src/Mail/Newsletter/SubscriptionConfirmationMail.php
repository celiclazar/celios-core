<?php

namespace Celios\Core\Mail\Newsletter;

use Celios\Core\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscriber $subscriber,
        public string $fromEmail,
        public string $fromName
    ) {
        $this->locale($subscriber->locale ?: config('locales.default', 'sr'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->fromEmail, $this->fromName),
            subject: __('newsletter.confirm_subject', ['app' => config('app.name', 'Celios CMS')]),
        );
    }

    public function content(): Content
    {
        $verificationUrl = route('newsletter.verify.localized', [
            'locale' => $this->subscriber->locale ?: 'sr',
            'token' => $this->subscriber->verification_token,
        ]);

        return new Content(
            view: 'emails.newsletter.confirmation',
            with: [
                'subscriber' => $this->subscriber,
                'verificationUrl' => $verificationUrl,
            ]
        );
    }
}
