<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('newsletter.verified_page_title') }} - {{ setting('site_name', 'Celios CMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface flex items-center justify-center min-h-screen p-4 text-on-surface antialiased">
    <div class="max-w-md w-full bg-surface-container-lowest rounded-2xl shadow-xl p-6 sm:p-8 text-center border border-surface-container-high/60">
        @if($status === 'success')
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-on-surface mb-2">{{ __('newsletter.verify_success_title') }}</h1>
            <p class="text-on-surface-variant mb-2 text-sm sm:text-base">{{ $message }}</p>
            @if(!empty($subscriber))
                <p class="text-xs sm:text-sm font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg inline-block mb-6 break-all">
                    {{ $subscriber->email }}
                </p>
            @endif
        @else
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-5">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-on-surface mb-2">{{ __('newsletter.notice') }}</h1>
            <p class="text-on-surface-variant mb-6 text-sm sm:text-base">{{ $message }}</p>
        @endif

        <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-primary hover:bg-primary-container text-on-primary font-medium rounded-xl transition duration-200 text-sm sm:text-base shadow-sm">
            {{ __('newsletter.back_to_home') }}
        </a>
    </div>
</body>
</html>
