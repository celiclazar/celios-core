<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    {{-- Kanonski URL - uvek forsiramo verziju sa jezičkim prefiksom --}}
    <link rel="canonical" href="{{ url('/' . app()->getLocale() . '/' . $page->getTranslation('slug', app()->getLocale())) }}" />

    {{-- Hreflang tagovi - povezujemo sve jezike ove stranice za Google robote --}}
    @foreach(['sr', 'en', 'it'] as $lang)
        @php
            $langSlug = $page->getTranslation('slug', $lang);
        @endphp
        @if(!empty($langSlug))
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ url("/{$lang}/{$langSlug}") }}" />
        @endif
    @endforeach
    {{-- Default fallback za robote --}}
    <link rel="alternate" hreflang="x-default" href="{{ url('/sr/' . $page->getTranslation('slug', 'sr')) }}" />


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Pouzdano uzimanje prevoda naslova stranice preko Spatie modela --}}
    <title>{{ $page->getTranslation('title', app()->getLocale()) }}</title>

    {{-- Globalni asset-i (Vite, Tailwind, itd.) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Swiper CSS za Slajder -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- GLightbox CSS za Galeriju -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen flex flex-col selection:bg-primary-fixed selection:text-on-primary-fixed">

@if(request()->has('preview') && auth()->check())
    <div class="sticky top-0 z-50 bg-amber-500 text-amber-950 px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium flex flex-wrap items-center justify-between gap-2 shadow-md">
        <div class="flex items-center space-x-2 min-w-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span class="truncate">
                @if(!empty($activeRevision))
                    {{ __('fields.preview_revision_mode', [
                        'date' => $activeRevision->created_at->format('d.m.Y H:i'),
                        'author' => $activeRevision->user?->name ?? 'System'
                    ]) }}
                @else
                    {{ __('fields.preview_draft_mode') }}
                @endif
            </span>
        </div>
        <a href="{{ route('filament.admin.resources.pages.edit', ['record' => $page->id]) }}" class="inline-flex items-center px-2.5 sm:px-3 py-1 bg-amber-900 text-white rounded text-xs font-semibold hover:bg-amber-950 transition shrink-0">
            {{ __('actions.back_to_editor') }} &rarr;
        </a>
    </div>
@endif

{{-- Navigation --}}
@include('partials.nav', ['currentPage' => $page])

{{-- Glavni sadržaj stranice --}}
<main class="w-full flex-1 flex flex-col">
    @php
        $blockModuleMap = [
            'posts_block' => 'blog',
            'documents_block' => 'documents',
            'form_block' => 'forms',
            'newsletter' => 'newsletter',
        ];
    @endphp

    @if(!empty($content) && is_array($content))
        @foreach($content as $block)
            @php
                $requiredModule = $blockModuleMap[$block['type']] ?? null;
            @endphp
            @if(!$requiredModule || module_enabled($requiredModule))
                @includeIf('components.blocks.' . $block['type'], ['data' => $block['data']])
            @endif
        @endforeach
    @else
        {{-- Fallback ako stranica nema dodate blokove --}}
        <div class="flex flex-col items-center justify-center py-24 px-4 text-center">
            <div class="p-4 bg-surface-container rounded-full text-primary mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <p class="text-on-surface-variant font-medium">{{ __('fields.no_previous_data') }}</p>
        </div>
    @endif
</main>

{{-- Footer & Modals --}}
@include('partials.footer')
@include('partials.celios-demo-modal')


<!-- Swiper & GLightbox JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Init Lightbox za galeriju
        const lightbox = GLightbox({
            selector: '.glightbox'
        });

        // Init Swiper za slajder
        document.querySelectorAll('.main-slider').forEach(function (element) {
            const isAutoplay = element.getAttribute('data-autoplay') === 'true';

            new Swiper(element, {
                loop: true,
                autoplay: isAutoplay ? { delay: 5000, disableOnInteraction: false } : false,
                pagination: { el: '.swiper-pagination', clickable: true },
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            });
        });
    });
</script></body>
</html>
