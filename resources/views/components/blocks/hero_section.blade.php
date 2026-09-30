@php
    $locale = app()->getLocale();
    $langMap = ['sr' => 'Srpski', 'en' => 'English', 'it' => 'Italiano'];
    $altLang = $langMap[$locale] ?? null;

    $get = function($field, $default = '') use ($data, $locale, $altLang) {
        if (!isset($data[$field])) return $default;
        if (is_array($data[$field])) {
            return $data[$field][$locale] 
                ?? ($altLang ? ($data[$field][$altLang] ?? null) : null)
                ?? $data[$field]['sr'] 
                ?? $data[$field]['Srpski'] 
                ?? $data[$field]['en'] 
                ?? $default;
        }
        return $data[$field] ?: $default;
    };

    $heading = $get('heading', '');
    $highlighted = $get('highlighted_text', '');
    $subtitle = $get('subtitle', '');
    $badgeText = $get('badge_text', $get('badge', ''));
    $badgeVersion = $get('badge_version', $get('version', ''));
    $badgeBuild = $get('badge_build', $get('build', ''));

    $primaryText = $get('button_primary_text', $get('button_text', ''));
    $primaryUrl = $data['button_primary_url'] ?? $data['button_url'] ?? '';

    $secondaryText = $get('button_secondary_text', '');
    $secondaryUrl = $data['button_secondary_url'] ?? '';

    $tertiaryText = $get('button_tertiary_text', '');
    $tertiaryUrl = $data['button_tertiary_url'] ?? '';

    $metrics = $data['metrics'] ?? $data['stats'] ?? [];

    // Izvlačenje slike iz Awcodes Curator-a
    $imageId = $data['background_image_id'] ?? null;
    $bgImageUrl = null;

    if ($imageId) {
        $mediaClass = config('curator.model', \Awcodes\Curator\Models\Media::class);
        $media = $mediaClass::find($imageId);
        $bgImageUrl = $media?->url ?? $media?->signedUrl;
    }

    $style = $data['style'] ?? [];
    $widthClass = $style['width'] ?? 'max-w-7xl';
    $minHeight = ($style['min_height'] ?? 'auto') === 'auto' ? '' : ($style['min_height'] ?? '');
    $paddingY = $style['padding_y'] ?? 'pt-space-12 pb-space-16';
    $rounded = $style['rounded'] ?? 'rounded-3xl';
    $align = $style['text_align'] ?? 'text-center';
    $cardStyle = ($style['card_style'] ?? 'flat') === 'card' ? 'bg-surface-container-lowest border border-surface-container-high/70 shadow-sm p-8 sm:p-12' : '';
@endphp

<!-- Ambient Subtle Glowing Orbs -->
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-24 right-1/4 w-[32rem] h-[32rem] bg-secondary-container/25 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute top-96 -left-24 w-96 h-96 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none -z-10"></div>

    @if($bgImageUrl)
        <div class="absolute inset-0 z-0 pointer-events-none">
            <img src="{{ $bgImageUrl }}"
                 class="w-full h-full object-cover opacity-10"
                 alt="{{ $heading ?? 'Hero background' }}">
            <div class="absolute inset-0 bg-gradient-to-b from-surface/80 via-surface/95 to-surface"></div>
        </div>
    @endif

    <!-- HERO SECTION -->
    <section class="mx-auto px-gutter-md sm:px-gutter-lg w-full relative z-10 {{ $widthClass }} {{ $minHeight }} {{ $paddingY }}">
        <div class="flex flex-col {{ $align === 'text-left' ? 'items-start text-left' : ($align === 'text-right' ? 'items-end text-right' : 'items-center text-center') }} {{ $rounded }} {{ $cardStyle }} max-w-4xl mx-auto gap-space-6">
            
            <!-- Badges & Status -->
            @if($badgeText || $badgeVersion || $badgeBuild)
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
            @endif

            <!-- Dramatic Typographic Headline -->
            @if($heading)
                <h1 class="font-display-lg text-3xl sm:text-4xl md:text-5xl lg:text-[56px] text-on-surface tracking-tight leading-[1.15] sm:leading-[1.12] font-bold break-words">
                    {{ $heading }}
                    @if($highlighted)
                        <span class="text-primary underline decoration-primary/30 underline-offset-4 sm:underline-offset-8">{{ $highlighted }}</span>
                    @endif
                </h1>
            @endif

            <!-- Lead Paragraph -->
            @if($subtitle)
                <p class="font-body-lg text-base sm:text-lg md:text-xl text-on-surface-variant max-w-3xl leading-relaxed break-words">
                    {{ $subtitle }}
                </p>
            @endif

            <!-- Hero Action CTAs -->
            @if($primaryText || $secondaryText || $tertiaryText)
                <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-center gap-3 sm:gap-space-3 pt-space-2 w-full sm:w-auto">
                    @if($primaryText)
                        <a href="{{ $primaryUrl ?: '#test-mode-banner' }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-space-2 px-space-6 py-3.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md font-semibold hover:bg-primary-container shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined text-xl text-on-primary">admin_panel_settings</span>
                            <span>{{ $primaryText }}</span>
                            <span class="ml-1 px-2 py-0.5 rounded text-[11px] bg-white/20 font-bold uppercase tracking-wider">Live</span>
                        </a>
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
            @endif

            <!-- Hero Visual Showcase Metrics Strip -->
            @if(!empty($metrics))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-space-4 w-full pt-space-6 sm:pt-space-8 text-left">
                    @foreach($metrics as $metric)
                        @php
                            $mLabel = is_array($metric['label'] ?? null) ? ($metric['label'][$locale] ?? $metric['label']['sr'] ?? $metric['label']['en'] ?? '') : ($metric['label'] ?? '');
                            $mVal = is_array($metric['value'] ?? ($metric['number'] ?? null)) ? ($metric['value'][$locale] ?? $metric['value']['sr'] ?? $metric['value']['en'] ?? '') : ($metric['value'] ?? ($metric['number'] ?? ''));
                            $mBadge = is_array($metric['badge'] ?? null) ? ($metric['badge'][$locale] ?? $metric['badge']['sr'] ?? $metric['badge']['en'] ?? '') : ($metric['badge'] ?? '');
                            $mNote = is_array($metric['note'] ?? null) ? ($metric['note'][$locale] ?? $metric['note']['sr'] ?? $metric['note']['en'] ?? '') : ($metric['note'] ?? '');
                        @endphp
                        <div class="p-4 sm:p-space-5 rounded-2xl bg-surface-container-lowest border border-surface-container-high/50 shadow-sm flex flex-col justify-between">
                            <span class="font-label-xs text-label-xs text-outline uppercase tracking-wider font-semibold">
                                {{ $mLabel }}
                            </span>
                            <div class="flex flex-wrap items-baseline gap-space-2 mt-space-2">
                                <span class="text-2xl sm:text-3xl font-bold text-on-surface font-display-lg">{{ $mVal }}</span>
                                @if(!empty($mBadge))
                                    <span class="font-label-xs text-label-xs text-on-tertiary-fixed-variant bg-tertiary-fixed px-space-1.5 py-0.5 rounded font-medium">
                                        {{ $mBadge }}
                                    </span>
                                @endif
                            </div>
                            @if(!empty($mNote))
                                <span class="font-body-sm text-body-sm text-on-surface-variant mt-1.5 break-words">{{ $mNote }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>
</div>
