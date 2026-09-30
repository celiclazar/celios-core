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
    $sectionTitle = $get('section_title', $get('heading', ''));
    $sectionDesc = $get('section_desc', $get('description', $get('subtitle', '')));

    $items = $data['features'] ?? $data['cards'] ?? $data['items'] ?? [];
@endphp

@if(!empty($items))
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="features">
    @if($sectionTitle || $sectionTag || $sectionDesc)
        <div class="text-center max-w-3xl mx-auto mb-space-8 sm:mb-space-12">
            @if($sectionTag)
                <span class="inline-block font-label-xs text-label-xs text-primary uppercase tracking-widest font-semibold bg-surface-container px-3 py-1 rounded-full">
                    {{ $sectionTag }}
                </span>
            @endif
            @if($sectionTitle)
                <h2 class="text-2xl sm:text-3xl lg:text-4xl text-on-surface font-bold mt-space-3 tracking-tight break-words">
                    {{ $sectionTitle }}
                </h2>
            @endif
            @if($sectionDesc)
                <p class="font-body-md text-body-md text-on-surface-variant mt-3 max-w-2xl mx-auto leading-relaxed">
                    {{ $sectionDesc }}
                </p>
            @endif
        </div>
    @endif

    <!-- Feature Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-space-6">
        @foreach($items as $card)
            @php
                $cardTitle = is_array($card['title'] ?? null) 
                    ? ($card['title'][$locale] ?? $card['title']['sr'] ?? $card['title']['en'] ?? '') 
                    : ($card['title'] ?? '');
                $cardDesc = is_array($card['description'] ?? null) 
                    ? ($card['description'][$locale] ?? $card['description']['sr'] ?? $card['description']['en'] ?? '') 
                    : ($card['description'] ?? '');
                $cardChecklist = is_array($card['checklist'] ?? null) 
                    ? ($card['checklist'][$locale] ?? $card['checklist']['sr'] ?? $card['checklist']['en'] ?? '') 
                    : ($card['checklist'] ?? '');
                $checklistItems = is_string($cardChecklist) 
                    ? array_filter(array_map('trim', explode("\n", $cardChecklist))) 
                    : (is_array($cardChecklist) ? $cardChecklist : []);
                $icon = $card['icon'] ?? 'widgets';
            @endphp
            <div class="rounded-2xl bg-surface-container-lowest p-5 sm:p-space-6 border border-surface-container-high/60 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-4">
                        <span class="material-symbols-outlined text-2xl">{{ $icon }}</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface font-semibold text-lg sm:text-xl break-words">
                        {{ $cardTitle }}
                    </h3>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-space-2 leading-relaxed break-words">
                        {{ $cardDesc }}
                    </p>
                </div>
                @if(!empty($checklistItems))
                    <div class="mt-space-6 pt-space-4 border-t border-surface-container">
                        <ul class="space-y-space-2 text-body-sm font-body-sm text-on-surface-variant">
                            @foreach($checklistItems as $item)
                                <li class="flex items-start sm:items-center gap-space-2">
                                    <span class="material-symbols-outlined text-primary text-base shrink-0 mt-0.5 sm:mt-0">check_circle</span>
                                    <span class="break-words">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif
