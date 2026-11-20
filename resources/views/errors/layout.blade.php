@php
    try {
        $locale = app()->getLocale();
        $brand = function_exists('current_brand') ? current_brand() : null;
        $siteName = $brand?->name ?: (function_exists('setting') ? setting('site_name', config('app.name', 'Celios CMS')) : config('app.name', 'Celios CMS'));
        $hasCustomLogo = $brand?->hasCustomLogo() ?? false;
        $logoLight = $brand?->logo_light_url;
        $logoDark = $brand?->logo_dark_url;
        $theme = function_exists('setting') ? setting('frontend_theme', 'modern-builder') : 'modern-builder';
    } catch (\Throwable $e) {
        $locale = app()->getLocale();
        $brand = null;
        $siteName = config('app.name', 'Celios CMS');
        $hasCustomLogo = false;
        $logoLight = null;
        $logoDark = null;
        $theme = 'modern-builder';
    }

    $code = trim($__env->yieldContent('code', '404'));
    $isServerError = in_array($code, ['500', '502', '503', '504']);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" 
      class="{{ in_array($theme, ['modern-builder', 'midnight']) ? 'dark' : '' }}" 
      data-frontend-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ $siteName }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen flex flex-col selection:bg-primary-fixed selection:text-on-primary-fixed">

    <!-- HEADER BAR -->
    <header class="w-full border-b border-surface-container bg-surface/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/' . $locale) }}" class="flex items-center gap-3 focus:outline-none group">
                @if($hasCustomLogo && $logoLight)
                    <img src="{{ $logoLight }}" alt="{{ $siteName }}" class="h-9 w-auto max-w-[180px] object-contain dark:hidden [html[data-frontend-theme='modern-builder']_&]:hidden [html[data-frontend-theme='midnight']_&]:hidden" />
                    <img src="{{ $logoDark ?: $logoLight }}" alt="{{ $siteName }}" class="h-9 w-auto max-w-[180px] object-contain brand-logo-ambient hidden dark:block [html[data-frontend-theme='modern-builder']_&]:block [html[data-frontend-theme='midnight']_&]:block" />
                @else
                    <span class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center font-bold text-lg shadow-sm">
                        {{ mb_strtoupper(mb_substr($siteName, 0, 1)) }}
                    </span>
                    <span class="text-xl font-bold tracking-tight text-on-surface">
                        {{ $siteName }}
                    </span>
                @endif
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ url('/' . $locale) }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-on-surface transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>{{ __('errors.action_home') }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN ERROR CONTENT -->
    <main class="flex-1 flex items-center justify-center px-4 py-12 sm:py-20 relative overflow-hidden">
        <!-- Ambient Background Glow Circles -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] {{ $isServerError ? 'bg-rose-500/10 dark:bg-rose-500/5' : 'bg-primary/10 dark:bg-primary/5' }} rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative w-full max-w-2xl bg-surface-container-low/90 backdrop-blur-xl border border-surface-container-high/60 rounded-3xl p-8 sm:p-14 shadow-2xl text-center space-y-6">
            
            <!-- Status Badge -->
            <div class="flex justify-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold tracking-wider uppercase {{ $isServerError ? 'bg-rose-500/15 text-rose-500 dark:text-rose-400 border border-rose-500/20' : 'bg-primary/15 text-primary border border-primary/20' }}">
                    <span class="w-2 h-2 rounded-full {{ $isServerError ? 'bg-rose-500 animate-pulse' : 'bg-primary animate-pulse' }}"></span>
                    <span>@yield('badge')</span>
                </div>
            </div>

            <!-- Big Display Code -->
            <div class="text-7xl sm:text-8xl font-black tracking-tight select-none">
                <span class="bg-gradient-to-r {{ $isServerError ? 'from-rose-500 via-amber-400 to-rose-400' : 'from-primary via-emerald-400 to-teal-300' }} bg-clip-text text-transparent">
                    @yield('code')
                </span>
            </div>

            <!-- Headline & Description -->
            <div class="space-y-3 max-w-lg mx-auto">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-on-surface">
                    @yield('title')
                </h1>
                <p class="text-sm sm:text-base text-on-surface-variant leading-relaxed">
                    @yield('message')
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/' . $locale) }}" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-primary text-on-primary hover:opacity-95 shadow-lg shadow-primary/25 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ __('errors.action_home') }}</span>
                </a>

                <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/' . $locale) }}'" 
                        type="button" 
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-surface-container-high hover:bg-surface-container-highest text-on-surface border border-surface-container-highest transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>{{ __('errors.action_back') }}</span>
                </button>

                @if(auth()->check())
                    <a href="{{ url('/admin') }}" 
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-surface-container-high hover:bg-surface-container-highest text-on-surface border border-surface-container-highest transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>{{ __('errors.action_admin') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="w-full border-t border-surface-container py-6 text-center text-xs text-on-surface-variant">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} {{ $siteName }}. {{ __('errors.all_rights_reserved') }}
        </div>
    </footer>
</body>
</html>
