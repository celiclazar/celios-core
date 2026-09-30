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

    $sectionTag = $get('section_tag', '');
    $heading = $get('heading', '');
    $subtitle = $get('subtitle', '');
    $stats = $data['stats'] ?? $data['metrics'] ?? [];
@endphp

@if(!empty($stats))
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="stats">
    @if($heading || $sectionTag || $subtitle)
        <div class="text-center max-w-3xl mx-auto mb-space-8 sm:mb-space-10">
            @if($sectionTag)
                <span class="inline-block font-label-xs text-label-xs text-primary uppercase tracking-widest font-semibold bg-surface-container px-3 py-1 rounded-full">
                    {{ $sectionTag }}
                </span>
            @endif
            @if($heading)
                <h2 class="text-2xl sm:text-3xl lg:text-4xl text-on-surface font-bold mt-space-3 tracking-tight break-words">
                    {{ $heading }}
                </h2>
            @endif
            @if($subtitle)
                <p class="font-body-md text-body-md text-on-surface-variant mt-3 max-w-2xl mx-auto leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-space-4 w-full text-left">
        @foreach($stats as $item)
            @php
                $label = is_array($item['label'] ?? null) 
                    ? ($item['label'][$locale] ?? $item['label']['sr'] ?? $item['label']['en'] ?? '') 
                    : ($item['label'] ?? '');
                $val = $item['number'] ?? $item['value'] ?? '';
                $badge = $item['badge'] ?? '';
                $note = is_array($item['note'] ?? null) 
                    ? ($item['note'][$locale] ?? $item['note']['sr'] ?? $item['note']['en'] ?? '') 
                    : ($item['note'] ?? '');
            @endphp
            <div class="p-4 sm:p-space-5 rounded-2xl bg-surface-container-lowest border border-surface-container-high/50 shadow-sm flex flex-col justify-between">
                <span class="font-label-xs text-label-xs text-outline uppercase tracking-wider font-semibold">
                    {{ $label }}
                </span>
                <div class="flex flex-wrap items-baseline gap-space-2 mt-space-2">
                    <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-surface font-display-lg">{{ $val }}</span>
                    @if(!empty($badge))
                        <span class="font-label-xs text-label-xs text-on-tertiary-fixed-variant bg-tertiary-fixed px-space-1.5 py-0.5 rounded font-medium">
                            {{ $badge }}
                        </span>
                    @endif
                </div>
                @if(!empty($note))
                    <span class="font-body-sm text-body-sm text-on-surface-variant mt-1.5 break-words">{{ $note }}</span>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif
