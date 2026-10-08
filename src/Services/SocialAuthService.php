<?php

namespace Celios\Core\Services;

use Celios\Core\Models\SocialAccount;
use Celios\Core\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    /**
     * Supported provider list.
     */
    protected array $supportedProviders = [
        'google',
        'github',
        'apple',
        'microsoft',
        'slack',
    ];

    /**
     * Determine if a given OAuth provider is supported.
     */
    public function isProviderSupported(string $provider): bool
    {
        return in_array(strtolower($provider), $this->supportedProviders, true);
    }

    /**
     * Get list of currently configured social providers.
     */
    public function getActiveProviders(): array
    {
        $providers = [];

        foreach ($this->supportedProviders as $provider) {
            if (config("services.{$provider}.client_id") && config("services.{$provider}.client_secret")) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    /**
     * Resolve or create a local User from an OAuth Socialite provider callback.
     */
    public function handleProviderCallback(string $provider, SocialiteUser $socialiteUser): User
    {
        return DB::transaction(function () use ($provider, $socialiteUser) {
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_user_id', $socialiteUser->getId())
                ->first();

            if ($socialAccount) {
                $socialAccount->update([
                    'token' => $socialiteUser->token,
                    'refresh_token' => $socialiteUser->refreshToken ?? null,
                    'token_expires_at' => isset($socialiteUser->expiresIn) ? now()->addSeconds($socialiteUser->expiresIn) : null,
                    'avatar_url' => $socialiteUser->getAvatar(),
                    'raw_data' => $socialiteUser->getRaw() ?? [],
                ]);

                return $socialAccount->user;
            }

            $email = $socialiteUser->getEmail();
            $user = null;

            if ($email) {
                $user = User::where('email', $email)->first();
            }

            if (! $user) {
                $baseUsername = Str::slug($socialiteUser->getNickname() ?: $socialiteUser->getName() ?: 'user', '_');
                $username = $baseUsername ?: 'user';
                $counter = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername.'_'.$counter++;
                }

                $user = User::create([
                    'name' => $socialiteUser->getName() ?: ($socialiteUser->getNickname() ?: 'User'),
                    'username' => $username,
                    'email' => $email ?: "{$provider}_{$socialiteUser->getId()}@social.celios.host",
                    'password' => Hash::make(Str::random(32)),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'avatar' => $socialiteUser->getAvatar(),
                ]);
            }

            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_user_id' => $socialiteUser->getId(),
                'token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken ?? null,
                'token_expires_at' => isset($socialiteUser->expiresIn) ? now()->addSeconds($socialiteUser->expiresIn) : null,
                'avatar_url' => $socialiteUser->getAvatar(),
                'raw_data' => $socialiteUser->getRaw() ?? [],
            ]);

            return $user;
        });
    }
}
