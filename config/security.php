<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Anti-Spam & Bot Mitigation (Cloudflare Turnstile / Google reCAPTCHA)
    |--------------------------------------------------------------------------
    |
    | Supported drivers: 'auto', 'turnstile', 'recaptcha', 'none'
    | If 'auto', the driver is selected based on which secret key is present.
    |
    */
    'captcha' => [
        'driver' => env('SECURITY_CAPTCHA_DRIVER', 'auto'),
        'turnstile' => [
            'site_key' => env('TURNSTILE_SITE_KEY', ''),
            'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
            'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        ],
        'recaptcha' => [
            'site_key' => env('RECAPTCHA_SITE_KEY', ''),
            'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
            'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
            'score_threshold' => (float) env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
        ],
        'timeout' => (int) env('SECURITY_CAPTCHA_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Malware & Antivirus Scanner
    |--------------------------------------------------------------------------
    |
    | Supported drivers: 'null', 'clamav'
    | 'clamav' connects via UNIX socket or TCP socket to ClamAV daemon.
    |
    */
    'scanner' => [
        'driver' => env('SECURITY_SCANNER_DRIVER', 'null'),
        'clamav' => [
            'socket' => env('CLAMAV_SOCKET', '/var/run/clamav/clamd.ctl'),
            'host' => env('CLAMAV_HOST', '127.0.0.1'),
            'port' => (int) env('CLAMAV_PORT', 3310),
            'timeout' => (int) env('CLAMAV_TIMEOUT', 10),
            'binary_path' => env('CLAMSCAN_PATH', '/usr/bin/clamscan'),
        ],
        // If true, scanning failures due to connection errors will fail closed (block upload)
        'fail_closed' => (bool) env('SECURITY_SCANNER_FAIL_CLOSED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP) & Cryptographic Nonces
    |--------------------------------------------------------------------------
    */
    'csp' => [
        'enabled' => (bool) env('SECURITY_CSP_ENABLED', true),
        'enable_nonce' => (bool) env('SECURITY_CSP_NONCE_ENABLED', true),
    ],
];
