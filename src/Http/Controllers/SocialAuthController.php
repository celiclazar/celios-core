<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SocialAuthController extends Controller
{
    public function __construct(
        protected SocialAuthService $socialAuthService
    ) {}

    /**
     * Redirect to the specified OAuth provider.
     */
    public function redirect(Request $request, string $provider): Response
    {
        if (! $this->socialAuthService->isProviderSupported($provider)) {
            abort(404, "OAuth provider [{$provider}] is not supported.");
        }

        // Store intent (e.g., admin panel login vs public frontend)
        if ($request->has('admin')) {
            session(['social_auth_admin' => true]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth provider callback.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        if (! $this->socialAuthService->isProviderSupported($provider)) {
            abort(404, "OAuth provider [{$provider}] is not supported.");
        }

        try {
            $socialiteUser = Socialite::driver($provider)->user();

            $user = $this->socialAuthService->handleProviderCallback($provider, $socialiteUser);

            Auth::login($user, remember: true);

            $request->session()->regenerate();

            if (session()->pull('social_auth_admin', false)) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Throwable $e) {
            return redirect()->route('login')->withErrors([
                'social' => 'Failed to authenticate with '.ucfirst($provider).': '.$e->getMessage(),
            ]);
        }
    }
}
