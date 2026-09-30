@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        if (!isset($data[$field])) return $default;
        if (is_array($data[$field])) {
            return $data[$field][$locale] 
                ?? $data[$field]['sr'] 
                ?? $data[$field]['en'] 
                ?? $default;
        }
        return $data[$field] ?: $default;
    };

    $pill = $get('status_pill', $get('pill', ''));
    $heading = $get('heading', 'Pokrenite Vaš Sledeći Projekat');
    $desc = $get('description', $get('text', ''));
    $btnText = $get('button_text', 'Saznaj Više');
    $btnUrl = $data['button_url'] ?? $data['admin_url'] ?? '#';
    $note1 = $get('footer_note_1', '');
    $note2 = $get('footer_note_2', '');

    $style = $data['style'] ?? [];
    $widthClass = $style['width'] ?? 'max-w-5xl';
    $minHeight = ($style['min_height'] ?? 'auto') === 'auto' ? '' : ($style['min_height'] ?? '');
    $paddingY = $style['padding_y'] ?? 'py-space-12';
    $rounded = $style['rounded'] ?? 'rounded-3xl';
    $align = $style['text_align'] ?? 'text-center';
    $cardStyle = ($style['card_style'] ?? 'card') === 'flat' ? 'bg-surface-container/50 border-0 shadow-none text-on-surface' : 'bg-gradient-to-br from-primary via-primary-container to-tertiary text-on-primary shadow-xl';
@endphp

<!-- POZIV NA AKCIJU (CTA) -->
<section class="mx-auto px-gutter-md sm:px-gutter-lg w-full {{ $widthClass }} {{ $minHeight }} {{ $paddingY }}" id="cta-block">
    <div class="{{ $rounded }} {{ $cardStyle }} p-6 sm:p-space-8 md:p-space-12 relative overflow-hidden">
        <!-- Decorative Background Blobs -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-black/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto text-center flex flex-col items-center gap-space-4 sm:gap-space-6">
            @if($pill)
                <div class="inline-flex items-center gap-space-2 px-space-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-on-primary font-label-xs text-label-xs uppercase tracking-wider font-bold">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>{{ $pill }}</span>
                </div>
            @endif

            @if($heading)
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-bold tracking-tight leading-tight break-words">
                    {{ $heading }}
                </h2>
            @endif

            @if($desc)
                <p class="text-base sm:text-lg md:text-xl text-on-primary-container max-w-2xl leading-relaxed">
                    {{ $desc }}
                </p>
            @endif

            <div class="flex flex-col sm:flex-row items-center justify-center gap-space-4 pt-space-2 w-full sm:w-auto">
                <a href="{{ $btnUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-space-3 px-6 sm:px-space-8 py-3.5 sm:py-4 rounded-xl bg-surface-container-lowest text-on-surface font-label-md text-label-md font-bold hover:bg-surface hover:shadow-2xl transition-all shadow-lg active:scale-95 group text-center">
                    <span class="material-symbols-outlined text-primary text-xl sm:text-2xl group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                    <span>{{ $btnText }}</span>
                </a>
            </div>

            @if($note1 || $note2)
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-on-primary-container font-label-xs text-label-xs mt-2 text-center">
                    @if($note1)
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">check_circle</span> {{ $note1 }}
                        </span>
                    @endif
                    @if($note1 && $note2)
                        <span class="hidden sm:inline">•</span>
                    @endif
                    @if($note2)
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">info</span> {{ $note2 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
