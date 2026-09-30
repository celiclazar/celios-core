@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $badgeText = $get('badge_text', 'Celios CMS • Laravel 11 + Filament v3');
    $badgeVersion = $get('badge_version', 'v3.4.2 Production Ready');
    $badgeBuild = $get('badge_build', 'build#8914-release');

    $heading = $get('heading', 'Modularni CMS kreiran za developere i dizajnere koji cene');
    $highlighted = $get('highlighted_text', 'brzinu, estetiku i kontrolu.');
    $subtitle = $get('subtitle', 'Potpuna sloboda u kreiranju modernih portfolio sajtova, digitalnih arhiva i višejezičnih publikacija bez sporih pluginova. Izgrađen na temeljima Laravel i Filament v3 ekosistema.');

    $primaryText = $get('button_primary_text', 'Pokreni Test Mode (Admin Demo)');
    $primaryUrl = $data['button_primary_url'] ?? '#test-mode-banner';

    $secondaryText = $get('button_secondary_text', 'Pregledaj Mogućnosti');
    $secondaryUrl = $data['button_secondary_url'] ?? '#mogucnosti';

    $tertiaryText = $get('button_tertiary_text', 'Pogledaj Portfolio');
    $tertiaryUrl = $data['button_tertiary_url'] ?? '#portfolio';

    $metrics = $data['metrics'] ?? [
        ['label' => 'Benchmark TTFB', 'value' => '32ms', 'badge' => '99.8th perc', 'note' => 'Laravel Octane + Redis keš'],
        ['label' => 'Lokalizacija', 'value' => '100% Native', 'badge' => 'SR / EN', 'note' => 'Nulta latencija u ruting-u'],
        ['label' => 'Filament v3', 'value' => '35+ Blokova', 'badge' => 'Reaktivno', 'note' => 'Livewire 3 + Alpine.js'],
        ['label' => 'Core Web Vitals', 'value' => '100 / 100', 'badge' => 'Green', 'note' => 'SEO & Pristupačnost spremni'],
    ];
@endphp

<!-- Ambient Subtle Glowing Orbs -->
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-24 right-1/4 w-[32rem] h-[32rem] bg-secondary-container/25 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute top-96 -left-24 w-96 h-96 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- HERO SECTION -->
    <section class="pt-space-8 pb-space-12 sm:pt-space-12 sm:pb-space-16 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full">
        <div class="flex flex-col items-center text-center max-w-4xl mx-auto gap-space-6">
            <!-- Badges & Status -->
            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-space-3">
                @if($badgeText)
                    <div class="inline-flex items-center gap-space-2 px-space-3 py-1 rounded-full bg-surface-container text-primary font-label-xs text-label-xs font-semibold shadow-sm border border-surface-container-high/60 max-w-full break-words">
                        <span class="w-2 h-2 rounded-full bg-primary shrink-0 animate-ping"></span>
                        <span class="uppercase tracking-wider break-words">{{ $badgeText }}</span>
                    </div>
                @endif
                @if($badgeVersion)
                    <span class="font-label-xs text-label-xs text-on-surface-variant bg-surface-container-lowest border border-surface-container px-space-3 py-1 rounded-full font-medium break-words">
                        {{ $badgeVersion }}
                    </span>
                @endif
                @if($badgeBuild)
                    <div class="hidden sm:flex items-center gap-1 font-code-sm text-code-sm text-on-surface-variant/80">
                        <span class="material-symbols-outlined text-sm text-tertiary">commit</span>
                        <span>{{ $badgeBuild }}</span>
                    </div>
                @endif
            </div>

            <!-- Dramatic Typographic Headline -->
            <h1 class="font-display-lg text-3xl sm:text-4xl md:text-5xl lg:text-[56px] text-on-surface tracking-tight leading-[1.15] sm:leading-[1.12] font-bold break-words">
                {{ $heading }}
                @if($highlighted)
                    <span class="text-primary underline decoration-primary/30 underline-offset-4 sm:underline-offset-8">{{ $highlighted }}</span>
                @endif
            </h1>

            <!-- Lead Paragraph -->
            @if($subtitle)
                <p class="font-body-lg text-base sm:text-lg md:text-xl text-on-surface-variant max-w-3xl leading-relaxed break-words">
                    {{ $subtitle }}
                </p>
            @endif

            <!-- Hero Action CTAs -->
            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-center gap-3 sm:gap-space-3 pt-space-2 w-full sm:w-auto">
                @if($primaryText)
                    <button class="w-full sm:w-auto inline-flex items-center justify-center gap-space-2 px-space-6 py-3.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md font-semibold hover:bg-primary-container shadow-md hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer" onclick="openDemoModal()">
                        <span class="material-symbols-outlined text-xl text-on-primary">admin_panel_settings</span>
                        <span>{{ $primaryText }}</span>
                        <span class="ml-1 px-2 py-0.5 rounded text-[11px] bg-white/20 font-bold uppercase tracking-wider">Live</span>
                    </button>
                @endif
                @if($secondaryText)
                    <a class="w-full sm:w-auto inline-flex items-center justify-center gap-space-2 px-space-5 py-3.5 rounded-xl bg-surface-container-lowest border border-surface-container-high text-on-surface font-label-md text-label-md font-semibold hover:bg-surface-container-low shadow-sm transition-all" href="{{ $secondaryUrl }}">
                        <span class="material-symbols-outlined text-primary text-xl">layers</span>
                        <span>{{ $secondaryText }}</span>
                    </a>
                @endif
                @if($tertiaryText)
                    <a class="w-full sm:w-auto inline-flex items-center justify-center gap-space-2 px-space-4 py-3.5 rounded-xl text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-colors" href="{{ $tertiaryUrl }}">
                        <span class="material-symbols-outlined text-lg">arrow_downward</span>
                        <span>{{ $tertiaryText }}</span>
                    </a>
                @endif
            </div>

            <!-- Hero Visual Showcase Metrics Strip -->
            @if(!empty($metrics))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-space-4 w-full pt-space-6 sm:pt-space-8 text-left">
                    @foreach($metrics as $metric)
                        <div class="p-4 sm:p-space-5 rounded-2xl bg-surface-container-lowest border border-surface-container-high/50 shadow-sm flex flex-col justify-between">
                            <span class="font-label-xs text-label-xs text-outline uppercase tracking-wider font-semibold">
                                {{ $metric['label'] ?? '' }}
                            </span>
                            <div class="flex flex-wrap items-baseline gap-space-2 mt-space-2">
                                <span class="text-2xl sm:text-3xl font-bold text-on-surface font-display-lg">{{ $metric['value'] ?? '' }}</span>
                                @if(!empty($metric['badge']))
                                    <span class="font-label-xs text-label-xs text-on-tertiary-fixed-variant bg-tertiary-fixed px-space-1.5 py-0.5 rounded font-medium">
                                        {{ $metric['badge'] }}
                                    </span>
                                @endif
                            </div>
                            @if(!empty($metric['note']))
                                <span class="font-body-sm text-body-sm text-on-surface-variant mt-1.5 break-words">{{ $metric['note'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
