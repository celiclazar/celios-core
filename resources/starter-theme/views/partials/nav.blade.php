@inject('menuResolver', 'App\Services\MenuResolverService')

@php
    // Fetch the menu assigned to the "primary" location in Celios Admin
    $headerItems = $menuResolver->getMenuForLocation('primary') ?? [];
    $locale = app()->getLocale();
    $brand = current_brand();
    $hasCustomLogo = $brand?->hasCustomLogo() ?? false;
@endphp

<!-- CELIOS STARTER THEME: HEADER NAVIGATION -->
<header class="theme-header sticky top-0 z-50 w-full bg-surface/90 backdrop-blur-md border-b border-surface-container" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between gap-4">
        
        <!-- Brand Logo / Site Title -->
        <a href="{{ url('/' . $locale) }}" class="flex items-center gap-3 focus:outline-none">
            @if($hasCustomLogo && $brand?->logo_light_url)
                <img src="{{ $brand->logo_light_url }}" alt="{{ $brand->name }}" class="h-9 w-auto max-w-[180px] object-contain dark:hidden" />
                <img src="{{ $brand->logo_dark_url ?: $brand->logo_light_url }}" alt="{{ $brand->name }}" class="h-9 w-auto max-w-[180px] object-contain hidden dark:block brand-logo-ambient" />
            @else
                <span class="text-xl font-bold tracking-tight text-on-surface">
                    {{ $brand?->name ?? 'Celios CMS' }}
                </span>
            @endif
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-on-surface-variant">
            @forelse($headerItems as $item)
                @if(!empty($item['children']))
                    {{-- Dropdown Item --}}
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button @click="open = !open" type="button" class="inline-flex items-center gap-1 hover:text-primary transition-colors py-1">
                            <span>{{ $item['title'] }}</span>
                            <span class="text-xs transition-transform" :class="{ 'rotate-180': open }">&#9662;</span>
                        </button>
                        <div x-show="open" 
                             x-transition 
                             class="absolute left-0 mt-2 w-48 rounded-xl bg-surface-container-low p-2 shadow-xl border border-surface-container" 
                             style="display: none;">
                            @foreach($item['children'] as $child)
                                <a href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}" class="block px-3 py-2 rounded-lg text-sm text-on-surface hover:bg-surface-container hover:text-primary transition">
                                    {{ $child['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Standard Link --}}
                    <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" class="hover:text-primary transition-colors py-1">
                        {{ $item['title'] }}
                    </a>
                @endif
            @empty
                <a href="#features" class="hover:text-primary transition-colors py-1">Features</a>
                <a href="#about" class="hover:text-primary transition-colors py-1">About</a>
                <a href="{{ url('/' . $locale . '/blog') }}" class="hover:text-primary transition-colors py-1">Blog</a>
            @endforelse
        </nav>

        <!-- Right Action Button / Language Switcher -->
        <div class="flex items-center gap-3">
            <a href="{{ url('/' . $locale) }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold rounded-xl bg-primary text-on-primary hover:opacity-90 transition">
                Get Started
            </a>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileOpen = !mobileOpen" type="button" class="md:hidden p-2 rounded-lg text-on-surface hover:bg-surface-container focus:outline-none" aria-label="Toggle Navigation">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Slide-out Menu -->
    <div x-show="mobileOpen" x-transition class="md:hidden px-4 pt-2 pb-6 border-t border-surface-container bg-surface space-y-2" style="display: none;">
        @foreach($headerItems as $item)
            <a href="{{ $item['url'] }}" class="block px-3 py-2 rounded-lg text-base font-medium text-on-surface hover:bg-surface-container">
                {{ $item['title'] }}
            </a>
        @endforeach
    </div>
</header>
