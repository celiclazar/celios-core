@php
    $socialAuthService = app(\Celios\Core\Services\SocialAuthService::class);
    $activeProviders = $socialAuthService->getActiveProviders();
@endphp

@if(!empty($activeProviders))
    <div class="mb-4 space-y-2">
        <div class="relative flex py-2 items-center">
            <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
            <span class="flex-shrink mx-4 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">Sign in with</span>
            <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
        </div>

        <div class="grid grid-cols-{{ count($activeProviders) > 1 ? '2' : '1' }} gap-2">
            @foreach($activeProviders as $provider)
                <a href="{{ route('auth.social.redirect', ['provider' => $provider, 'admin' => 1]) }}"
                   class="flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <span class="capitalize">{{ $provider }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
