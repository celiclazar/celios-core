<?php

namespace Celios\Core\Services\Security;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaptchaService
{
    /**
     * Determine active driver: 'turnstile', 'recaptcha', or 'none'.
     */
    public function getDriver(): string
    {
        $driver = config('security.captcha.driver', 'auto');

        if ($driver === 'auto') {
            if (! empty(config('security.captcha.turnstile.secret_key'))) {
                return 'turnstile';
            }
            if (! empty(config('security.captcha.recaptcha.secret_key'))) {
                return 'recaptcha';
            }

            return 'none';
        }

        return $driver ?: 'none';
    }

    /**
     * Check if captcha verification is required/configured.
     */
    public function isEnabled(): bool
    {
        $driver = $this->getDriver();

        if ($driver === 'none') {
            return false;
        }

        if ($driver === 'turnstile') {
            return ! empty(config('security.captcha.turnstile.secret_key'));
        }

        if ($driver === 'recaptcha') {
            return ! empty(config('security.captcha.recaptcha.secret_key'));
        }

        return false;
    }

    /**
     * Return public site key for frontend rendering.
     */
    public function getSiteKey(): ?string
    {
        $driver = $this->getDriver();

        return match ($driver) {
            'turnstile' => config('security.captcha.turnstile.site_key'),
            'recaptcha' => config('security.captcha.recaptcha.site_key'),
            default => null,
        };
    }

    /**
     * Verify captcha response token.
     */
    public function verify(?string $token, ?string $ip = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (empty($token)) {
            Log::warning('[SECURITY_AUDIT] Captcha verification failed: empty token received', [
                'ip' => $ip ?? request()->ip(),
                'driver' => $this->getDriver(),
            ]);

            return false;
        }

        $driver = $this->getDriver();

        return match ($driver) {
            'turnstile' => $this->verifyTurnstile($token, $ip),
            'recaptcha' => $this->verifyRecaptcha($token, $ip),
            default => true,
        };
    }

    protected function verifyTurnstile(string $token, ?string $ip = null): bool
    {
        $secret = config('security.captcha.turnstile.secret_key');
        $verifyUrl = config('security.captcha.turnstile.verify_url', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
        $timeout = config('security.captcha.timeout', 5);

        try {
            $response = Http::asForm()->timeout($timeout)->post($verifyUrl, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip ?? request()->ip(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $success = (bool) ($data['success'] ?? false);

                if (! $success) {
                    Log::warning('[SECURITY_AUDIT] Cloudflare Turnstile rejection', [
                        'error_codes' => $data['error-codes'] ?? [],
                        'ip' => $ip ?? request()->ip(),
                    ]);
                }

                return $success;
            }
        } catch (\Throwable $e) {
            Log::error('[SECURITY_AUDIT] Cloudflare Turnstile API exception', [
                'message' => $e->getMessage(),
                'ip' => $ip ?? request()->ip(),
            ]);
        }

        return false;
    }

    protected function verifyRecaptcha(string $token, ?string $ip = null): bool
    {
        $secret = config('security.captcha.recaptcha.secret_key');
        $verifyUrl = config('security.captcha.recaptcha.verify_url', 'https://www.google.com/recaptcha/api/siteverify');
        $minScore = (float) config('security.captcha.recaptcha.score_threshold', 0.5);
        $timeout = config('security.captcha.timeout', 5);

        try {
            $response = Http::asForm()->timeout($timeout)->post($verifyUrl, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip ?? request()->ip(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $success = (bool) ($data['success'] ?? false);

                if (! $success) {
                    Log::warning('[SECURITY_AUDIT] Google reCAPTCHA rejection', [
                        'error_codes' => $data['error-codes'] ?? [],
                        'ip' => $ip ?? request()->ip(),
                    ]);

                    return false;
                }

                // If v3 returns a score, check score threshold
                if (isset($data['score'])) {
                    $score = (float) $data['score'];
                    if ($score < $minScore) {
                        Log::warning('[SECURITY_AUDIT] Google reCAPTCHA score below threshold', [
                            'score' => $score,
                            'threshold' => $minScore,
                            'ip' => $ip ?? request()->ip(),
                        ]);

                        return false;
                    }
                }

                return true;
            }
        } catch (\Throwable $e) {
            Log::error('[SECURITY_AUDIT] Google reCAPTCHA API exception', [
                'message' => $e->getMessage(),
                'ip' => $ip ?? request()->ip(),
            ]);
        }

        return false;
    }
}
