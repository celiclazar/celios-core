<?php

namespace Celios\Core\Services\Newsletter;

use Celios\Core\Mail\Newsletter\NewsletterCampaignMail;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;

class NewsletterMailerService
{
    /**
     * Resolve the configured Mailer instance.
     */
    public function getMailer(): Mailer
    {
        $mailerType = setting('newsletter_mailer_type', 'app_default');

        if ($mailerType === 'custom_smtp') {
            $host = setting('newsletter_smtp_host', '127.0.0.1');
            $port = (int) setting('newsletter_smtp_port', 587);
            $username = setting('newsletter_smtp_username');
            $password = setting('newsletter_smtp_password');
            $encryption = setting('newsletter_smtp_encryption', 'tls');
            $scheme = ($encryption === 'ssl' || $port === 465) ? 'smtps' : null;

            Config::set('mail.mailers.newsletter_smtp', [
                'transport' => 'smtp',
                'scheme' => $scheme,
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'timeout' => 15,
            ]);

            return Mail::mailer('newsletter_smtp');
        }

        if ($mailerType === 'ses' && config('mail.mailers.ses')) {
            return Mail::mailer('ses');
        }

        if ($mailerType === 'resend' && config('mail.mailers.resend')) {
            return Mail::mailer('resend');
        }

        if ($mailerType === 'mailgun' && config('mail.mailers.mailgun')) {
            return Mail::mailer('mailgun');
        }

        // Fallback to default Laravel mailer
        return Mail::mailer();
    }

    public function getFromAddress(): string
    {
        return setting('newsletter_from_email')
            ?: config('mail.from.address', 'hello@example.com');
    }

    public function getFromName(): string
    {
        return setting('newsletter_from_name')
            ?: config('mail.from.name', config('app.name', 'Celios CMS'));
    }

    /**
     * Rate limit in emails per minute. 0 means unlimited / fast (for SES, Resend, etc.).
     */
    public function getRateLimit(): int
    {
        return (int) setting('newsletter_rate_limit', 0);
    }

    /**
     * Calculate delay offset in seconds for a given subscriber index when throttling is active.
     */
    public function calculateDelaySeconds(int $index): int
    {
        $ratePerMinute = $this->getRateLimit();
        if ($ratePerMinute <= 0) {
            return 0;
        }

        // For example: 20 emails/min => 1 email every 3 seconds
        $secondsPerEmail = 60 / $ratePerMinute;
        return (int) round($index * $secondsPerEmail);
    }

    /**
     * Test sending a verification/ping email using the current configuration.
     */
    public function testConnection(string $toEmail): void
    {
        $mailer = $this->getMailer();
        $fromEmail = $this->getFromAddress();
        $fromName = $this->getFromName();

        $mailer->raw(__('newsletter.test_connection_body', [
            'app' => config('app.name', 'Celios CMS'),
            'date' => now()->toDateTimeString(),
        ]), function ($message) use ($toEmail, $fromEmail, $fromName) {
            $message->to($toEmail)
                ->from($fromEmail, $fromName)
                ->subject(__('newsletter.test_connection_subject'));
        });
    }
}
