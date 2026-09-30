<?php

namespace Celios\Core\Mail\Newsletter;

use Celios\Core\Models\NewsletterCampaign;
use Celios\Core\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterCampaign $campaign,
        public NewsletterSubscriber $subscriber,
        public string $subjectLine,
        public string $contentHtml,
        public string $unsubscribeUrl,
        public ?string $trackingUrl,
        public string $fromEmail,
        public string $fromName
    ) {
        $this->locale($subscriber->locale ?: config('locales.default', 'sr'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->fromEmail, $this->fromName),
            subject: $this->subjectLine,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$this->unsubscribeUrl}>",
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.campaign',
            with: [
                'campaign' => $this->campaign,
                'subscriber' => $this->subscriber,
                'contentHtml' => $this->contentHtml,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'trackingUrl' => $this->trackingUrl,
            ]
        );
    }
}
