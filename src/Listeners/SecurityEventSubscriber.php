<?php

namespace Celios\Core\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class SecurityEventSubscriber
{
    public function handleFailedLogin(Failed $event): void
    {
        Log::warning('[SECURITY_AUDIT] Failed login attempt', [
            'guard' => $event->guard,
            'user_id' => $event->user?->getAuthIdentifier(),
            'email' => $event->credentials['email'] ?? null,
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function handleLockout(Lockout $event): void
    {
        Log::alert('[SECURITY_AUDIT] Account lockout triggered (excessive failed attempts)', [
            'request' => $event->request->except(['password']),
            'ip' => $event->request->ip(),
            'user_agent' => substr((string) $event->request->userAgent(), 0, 255),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        Log::info('[SECURITY_AUDIT] Password reset performed', [
            'user_id' => $event->user->getAuthIdentifier(),
            'email' => $event->user->email ?? null,
            'ip' => request()->ip(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            Failed::class => 'handleFailedLogin',
            Lockout::class => 'handleLockout',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }
}
