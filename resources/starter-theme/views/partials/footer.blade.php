@inject('menuResolver', 'App\Services\MenuResolverService')

@php
    $footerItems = $menuResolver->getMenuForLocation('footer') ?? [];
    $locale = app()->getLocale();
    $brand = current_brand();
@endphp

<!-- CELIOS STARTER THEME: FOOTER -->
<footer class="theme-footer w-full bg-surface-container-low border-t border-surface-container pt-12 pb-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-surface-container">
            
            <!-- Brand & Tagline -->
            <div class="space-y-1 text-center md:text-left">
                <span class="text-lg font-bold tracking-tight text-on-surface">
                    {{ $brand?->name ?? 'Celios CMS' }}
                </span>
                <p class="text-xs text-on-surface-variant">
                    {{ $brand?->tagline ?? 'Modern, headless-ready website engine.' }}
                </p>
            </div>

            <!-- Footer Navigation Links -->
            @if(!empty($footerItems))
                <nav class="flex flex-wrap items-center justify-center gap-6 text-sm text-on-surface-variant font-medium">
                    @foreach($footerItems as $item)
                        <a href="{{ $item['url'] }}" target="{{ $item['target'] ?? '_self' }}" class="hover:text-primary transition-colors">
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>

        <!-- Copyright -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-on-surface-variant">
            <div>
                &copy; {{ date('Y') }} {{ $brand?->name ?? 'Celios CMS' }}. All rights reserved.
            </div>
            <div>
                Crafted with <span class="text-primary font-semibold">Celios</span>
            </div>
        </div>
    </div>
</footer>
