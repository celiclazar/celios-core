@inject('menuResolver', 'App\Services\MenuResolverService')

@php
    $footerItems = $menuResolver->getMenu('footer') ?? [];
    $locale = app()->getLocale();
@endphp

<!-- PODNOŽJE (FOOTER) -->
<footer class="w-full bg-surface-container-low border-t border-surface-container-high/60 pt-space-12 pb-space-8 mt-auto">
    <div class="max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-y-8 gap-x-6 sm:gap-space-8 pb-space-8 border-b border-surface-container">
            <!-- Brand Info -->
            <div class="sm:col-span-2 md:col-span-5 space-y-space-3">
                <a aria-label="Home" class="flex items-center gap-space-3 group focus:outline-none" href="{{ url('/' . $locale) }}">
                    <span class="font-display-lg text-2xl font-bold tracking-tight text-on-surface flex items-center gap-1.5">
                        <span class="w-8 h-8 rounded-xl bg-primary text-on-primary flex items-center justify-center font-bold text-lg shadow-sm">
                            C
                        </span>
                        <span>Celios<span class="text-primary">.cms</span></span>
                    </span>
                </a>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-sm leading-relaxed">
                    {{ $locale === 'sr' ? 'Open, stabilan i modularan CMS engine kreiran za programere, digitalne studije i autore sadržaja širom sveta.' : 'Open, reliable, and modular CMS engine engineered for developers, digital studios, and content creators.' }}
                </p>
                <div class="flex items-center gap-space-2 pt-space-1">
                    <span class="font-label-xs text-label-xs bg-surface-container px-2.5 py-1 rounded font-semibold text-primary">v3.4.2 Engine</span>
                    <span class="font-label-xs text-label-xs text-on-surface-variant">MIT Licensed</span>
                </div>
            </div>

            <!-- Links Columns -->
            <div class="sm:col-span-1 md:col-span-2">
                <h4 class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-outline mb-space-3">
                    {{ $locale === 'sr' ? 'Navigacija' : 'Navigation' }}
                </h4>
                <ul class="space-y-space-2 font-body-sm text-body-sm text-on-surface-variant">
                    @if(!empty($footerItems))
                        @foreach($footerItems as $item)
                            <li>
                                <a class="hover:text-primary transition-colors" href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}">
                                    {{ $item['title'] }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li><a class="hover:text-primary transition-colors" href="#mogucnosti">{{ $locale === 'sr' ? 'Mogućnosti' : 'Features' }}</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#modularni-blokovi">{{ $locale === 'sr' ? 'Blokovi & Layouti' : 'Layout Builder' }}</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#portfolio">{{ $locale === 'sr' ? 'Primeri Sajtova' : 'Selected Work' }}</a></li>
                        <li><a class="hover:text-primary transition-colors font-medium text-primary" href="{{ url('/admin') }}">Admin Demo</a></li>
                    @endif
                </ul>
            </div>

            <div class="sm:col-span-1 md:col-span-2">
                <h4 class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-outline mb-space-3">
                    {{ $locale === 'sr' ? 'Resursi' : 'Resources' }}
                </h4>
                <ul class="space-y-space-2 font-body-sm text-body-sm text-on-surface-variant">
                    <li><a class="hover:text-primary transition-colors" href="{{ url('/' . $locale . '/blog') }}">{{ $locale === 'sr' ? 'Blog & Vesti' : 'Blog & News' }}</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ url('/' . $locale . '/documents') }}">{{ $locale === 'sr' ? 'Dokumentacija' : 'Documentation' }}</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ url('/sitemap.xml') }}">Sitemap.xml</a></li>
                </ul>
            </div>

            <div class="sm:col-span-2 md:col-span-3">
                <h4 class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-outline mb-space-3">
                    {{ $locale === 'sr' ? 'Kontakt & Demo' : 'Contact & Demo' }}
                </h4>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-2">
                    {{ $locale === 'sr' ? 'Dostupno za razvoj, arhitekturu i proširenje modularnih sistema.' : 'Available for bespoke architecture and scalable modular applications.' }}
                </p>
                <a class="inline-flex items-center gap-1 text-primary font-label-sm text-label-sm font-semibold hover:underline" href="{{ url('/admin') }}">
                    <span>{{ $locale === 'sr' ? 'Otvori Test Mode Demo' : 'Launch Admin Demo' }}</span>
                    <span class="material-symbols-outlined text-sm">north_east</span>
                </a>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-space-6 flex flex-col sm:flex-row items-center justify-between gap-space-4 font-body-sm text-body-sm text-on-surface-variant text-center sm:text-left">
            <p>© {{ date('Y') }} Celios CMS. {{ $locale === 'sr' ? 'Sva prava zadržana.' : 'All rights reserved.' }}</p>
            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-space-4">
                <a class="hover:text-on-surface transition-colors" href="{{ url('/' . $locale . '/politika-privatnosti') }}">{{ $locale === 'sr' ? 'Privatnost' : 'Privacy' }}</a>
                <a class="hover:text-on-surface transition-colors" href="{{ url('/' . $locale . '/uslovi-koriscenja') }}">{{ $locale === 'sr' ? 'Uslovi' : 'Terms' }}</a>
                @if((bool) setting('cookie_consent_enabled', '1'))
                    <button type="button"
                        onclick="if(window.CeliosConsent) { window.CeliosConsent.openModal(); } else { window.dispatchEvent(new CustomEvent('celios:open-cookie-preferences')); }"
                        class="hover:text-on-surface transition-colors flex items-center gap-1 cursor-pointer focus:outline-none">
                        <span class="material-symbols-outlined text-sm">cookie</span>
                        <span>{{ __('cookies.ui_cookie_settings_link') }}</span>
                    </button>
                @endif
                <span class="text-xs uppercase bg-surface-container px-2 py-0.5 rounded font-mono">{{ $locale }}</span>
            </div>
        </div>
    </div>
</footer>

{{-- Global Cookie Management Consent Banner & Script Engine --}}
@include('partials.cookie-consent')

