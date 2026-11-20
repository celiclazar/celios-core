<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ in_array(setting('frontend_theme', 'ocean'), ['modern-builder', 'midnight']) ? 'dark' : '' }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{ url('/' . app()->getLocale() . '/' . $page->getTranslation('slug', app()->getLocale())) }}" />

    {{-- Multilingual Hreflang Tags --}}
    @foreach(['sr', 'en', 'it'] as $lang)
        @php $langSlug = $page->getTranslation('slug', $lang); @endphp
        @if(!empty($langSlug))
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ url("/{$lang}/{$langSlug}") }}" />
        @endif
    @endforeach

    <title>{{ $page->getTranslation('title', app()->getLocale()) }} - {{ current_brand()?->name ?? 'Celios' }}</title>

    {{-- Global Styles & Fonts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('themes/starter-theme/assets/css/theme.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen flex flex-col">

    {{-- Theme Header / Navigation --}}
    @include('partials.nav', ['currentPage' => $page])

    {{-- Main Content: Loops through page builder modular blocks --}}
    <main class="w-full flex-1 flex flex-col">
        @if(!empty($content) && is_array($content))
            @foreach($content as $block)
                @includeIf('components.blocks.' . $block['type'], ['data' => $block['data']])
            @endforeach
        @else
            <div class="py-24 text-center">
                <p class="text-on-surface-variant font-medium">Page has no content blocks yet.</p>
            </div>
        @endif
    </main>

    {{-- Theme Footer --}}
    @include('partials.footer')

    <script src="{{ asset('themes/starter-theme/assets/js/theme.js') }}"></script>
</body>
</html>
