<?php

namespace Celios\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach OWASP recommended HTTP security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Generate and bind dynamic CSP nonce before processing request
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);

        if (class_exists(\Illuminate\Support\Facades\Vite::class)) {
            \Illuminate\Support\Facades\Vite::useCspNonce($nonce);
        }

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 2. Add Content-Security-Policy header if not already present
        if (! $response->headers->has('Content-Security-Policy')) {
            $useNonce = config('security.csp.enable_nonce', true) && ! $request->is('docs/*');
            $nonceToken = $useNonce ? " 'nonce-{$nonce}'" : '';

            $csp = [
                "default-src 'self'",
                "script-src 'self'{$nonceToken} 'unsafe-inline' 'unsafe-eval' https:",
                "style-src 'self'{$nonceToken} 'unsafe-inline' https: https://fonts.googleapis.com",
                "font-src 'self' data: https: https://fonts.gstatic.com",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' https: wss:",
                "frame-ancestors 'self'",
                "object-src 'none'",
                "base-uri 'self'",
            ];

            $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        return $response;
    }
}
