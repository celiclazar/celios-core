@inject('menuResolver', 'App\Services\MenuResolverService')

@php
    $headerItems = $menuResolver->getMenu('header') ?? [];
    $locale = app()->getLocale();
@endphp

<!-- TOP HEADER / NAVIGATION -->
<header class="sticky top-0 z-50 w-full bg-surface/90 backdrop-blur-xl border-b border-surface-container shadow-[0_1px_8px_rgba(0,0,0,0.03)] transition-all" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg h-20 flex items-center justify-between gap-space-4">
        
        <!-- Brand Logo -->
        <a aria-label="Home" class="flex items-center gap-space-3 group focus:outline-none" href="{{ url('/' . $locale) }}">
            <span class="font-display-lg text-2xl font-bold tracking-tight text-on-surface flex items-center gap-1.5">
                <span class="w-8 h-8 rounded-xl bg-primary text-on-primary flex items-center justify-center font-bold text-lg shadow-sm">
                    C
                </span>
                <span>Celios<span class="text-primary">.cms</span></span>
            </span>
        </a>

        <!-- Center Navigation Links (Dynamic CMS Menu with Fallback) -->
        <nav class="hidden md:flex items-center gap-space-6 font-label-md text-label-md text-on-surface-variant font-medium">
            @if(!empty($headerItems))
                @foreach($headerItems as $item)
                    @if(!empty($item['children']))
                        {{-- Dropdown stavka --}}
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button @click="open = !open"
                                    type="button"
                                    class="inline-flex items-center gap-1 hover:text-primary transition-colors py-1 focus:outline-none">
                                <span>{{ $item['title'] }}</span>
                                <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="{ 'rotate-180': open }">expand_more</span>
                            </button>

                            {{-- Dropdown panel --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute left-0 z-50 mt-1 w-56 origin-top-left rounded-2xl bg-surface-container-lowest p-2 shadow-xl border border-surface-container-high/70 focus:outline-none"
                                 style="display: none;">
                                @if($item['url'] && $item['url'] !== '#')
                                    <a href="{{ $item['url'] }}"
                                       target="{{ $item['target'] ?? '_self' }}"
                                       class="block rounded-xl px-3 py-2 text-xs font-semibold text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors border-b border-surface-container-high/40 mb-1">
                                        {{ $item['title'] }} &rarr;
                                    </a>
                                @endif

                                @foreach($item['children'] as $child)
                                    <a href="{{ $child['url'] }}"
                                       target="{{ $child['target'] ?? '_self' }}"
                                       class="block rounded-xl px-3 py-2 text-sm font-medium text-on-surface hover:bg-surface-container hover:text-primary transition-colors">
                                        {{ $child['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- Običan link --}}
                        <a href="{{ $item['url'] }}"
                           target="{{ $item['target'] ?? '_self' }}"
                           class="hover:text-primary transition-colors py-1">
                            {{ $item['title'] }}
                        </a>
                    @endif
                @endforeach
            @else
                {{-- Default CMS Section Anchor Links --}}
                <a class="hover:text-primary transition-colors py-1" href="#mogucnosti">{{ $locale === 'sr' ? 'Mogućnosti' : ($locale === 'it' ? 'Funzionalità' : 'Features') }}</a>
                <a class="hover:text-primary transition-colors py-1" href="#modularni-blokovi">{{ $locale === 'sr' ? 'Blokovi' : ($locale === 'it' ? 'Blocchi' : 'Blocks') }}</a>
                <a class="hover:text-primary transition-colors py-1" href="#portfolio">{{ $locale === 'sr' ? 'Portfolio' : 'Portfolio' }}</a>
                <a class="hover:text-primary transition-colors py-1" href="{{ url('/' . $locale . '/blog') }}">{{ $locale === 'sr' ? 'Blog' : 'Blog' }}</a>
            @endif
        </nav>

        <!-- Right Utility Actions -->
        <div class="flex items-center gap-2 sm:gap-space-3">
            <!-- Language Switcher SR / EN / IT -->
            <div class="flex items-center p-0.5 bg-surface-container rounded-lg border border-surface-container-high/60">
                @foreach(['sr' => 'SR', 'en' => 'EN', 'it' => 'IT'] as $code => $label)
                    @php
                        if (isset($currentPost)) {
                            $localizedSlug = $currentPost->getTranslation('slug', $code);
                            $langUrl = !empty($localizedSlug) ? url("/{$code}/blog/{$localizedSlug}") : url("/{$code}/blog");
                        } elseif (isset($currentCategory) && $currentCategory) {
                            $langUrl = $currentCategory->getUrl($code);
                        } elseif (isset($isBlogIndex) && $isBlogIndex) {
                            $langUrl = url("/{$code}/blog");
                        } elseif (isset($currentPage) && method_exists($currentPage, 'getTranslation')) {
                            $localizedSlug = $currentPage->getTranslation('slug', $code);
                            $langUrl = !empty($localizedSlug) ? url("/{$code}/{$localizedSlug}") : url("/{$code}");
                        } else {
                            $langUrl = route('language.switch', ['locale' => $code]);
                        }

                        if (request()->has('preview')) {
                            $langUrl .= (str_contains($langUrl, '?') ? '&' : '?') . 'preview=true';
                        }
                    @endphp
                    <a href="{{ $langUrl }}"
                       class="px-2 sm:px-2.5 py-1 rounded-md text-xs sm:text-label-xs transition-colors {{ $locale === $code ? 'bg-surface-container-lowest text-on-surface font-semibold shadow-[0_1px_2px_rgba(15,23,42,0.06)]' : 'text-on-surface-variant hover:text-on-surface font-medium' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container focus:outline-none cursor-pointer" aria-label="Toggle Navigation Menu">
                <span class="material-symbols-outlined text-2xl" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div x-show="mobileMenuOpen"
         @click.outside="mobileMenuOpen = false"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-surface-container bg-surface-container-lowest/95 backdrop-blur-xl px-4 pt-3 pb-5 space-y-2 shadow-xl"
         style="display: none;">
        @if(!empty($headerItems))
            @foreach($headerItems as $item)
                @if(!empty($item['children']))
                    <div x-data="{ subOpen: false }" class="py-1">
                        <button @click="subOpen = !subOpen" type="button" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-base font-medium text-on-surface hover:bg-surface-container hover:text-primary">
                            <span>{{ $item['title'] }}</span>
                            <span class="material-symbols-outlined text-base transition-transform duration-200" :class="{ 'rotate-180': subOpen }">expand_more</span>
                        </button>
                        <div x-show="subOpen" class="pl-4 space-y-1 mt-1">
                            @if($item['url'] && $item['url'] !== '#')
                                <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-on-surface-variant hover:text-primary">
                                    {{ $item['title'] }} ({{ __('actions.view') ?? 'Pregled' }})
                                </a>
                            @endif
                            @foreach($item['children'] as $child)
                                <a href="{{ $child['url'] }}" target="{{ $child['target'] ?? '_self' }}" class="block px-3 py-1.5 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container hover:text-primary">
                                    {{ $child['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" class="block px-3 py-2 rounded-xl text-base font-medium text-on-surface hover:bg-surface-container hover:text-primary">
                        {{ $item['title'] }}
                    </a>
                @endif
            @endforeach
        @else
            <a class="block px-3 py-2 rounded-xl text-base font-medium text-on-surface hover:bg-surface-container hover:text-primary" href="#mogucnosti">{{ $locale === 'sr' ? 'Mogućnosti' : 'Features' }}</a>
            <a class="block px-3 py-2 rounded-xl text-base font-medium text-on-surface hover:bg-surface-container hover:text-primary" href="#modularni-blokovi">{{ $locale === 'sr' ? 'Blokovi & Layouti' : 'Blocks & Layouts' }}</a>
            <a class="block px-3 py-2 rounded-xl text-base font-medium text-on-surface hover:bg-surface-container hover:text-primary" href="#portfolio">{{ $locale === 'sr' ? 'Portfolio' : 'Portfolio' }}</a>
            <a class="block px-3 py-2 rounded-xl text-base font-medium text-on-surface hover:bg-surface-container hover:text-primary" href="{{ url('/' . $locale . '/blog') }}">{{ $locale === 'sr' ? 'Blog' : 'Blog' }}</a>
        @endif
        <div class="pt-2 border-t border-surface-container">
            <a class="block px-3 py-2 rounded-xl text-base font-medium text-primary hover:bg-surface-container" href="{{ url('/admin') }}">
                Admin Demo &rarr;
            </a>
        </div>
    </div>
</header>
