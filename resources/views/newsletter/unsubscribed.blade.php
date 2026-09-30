<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('newsletter.unsubscribed_page_title') }} - {{ setting('site_name', 'Celios CMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface flex items-center justify-center min-h-screen p-4 text-on-surface antialiased">
    <div class="max-w-md w-full bg-surface-container-lowest rounded-2xl shadow-xl p-6 sm:p-8 text-center border border-surface-container-high/60">
        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-surface-container text-on-surface-variant rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5">
            <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
        </div>

        <h1 class="text-xl sm:text-2xl font-bold text-on-surface mb-2">{{ __('newsletter.unsubscribed_title') }}</h1>
        <p class="text-on-surface-variant mb-6 text-sm sm:text-base break-all">
            {{ __('newsletter.unsubscribed_desc', ['email' => $subscriber->email]) }}
        </p>

        <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-primary-container text-on-primary font-medium rounded-xl transition duration-200 text-sm sm:text-base shadow-sm">
            {{ __('newsletter.back_to_home') }}
        </a>
    </div>
</body>
</html>
