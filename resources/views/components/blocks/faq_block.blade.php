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

    $sectionTag = $get('section_tag', __('blocks.faq_title') ?? 'FAQ');
    $heading = $get('heading', $get('title', 'Često Postavljana Pitanja'));
    $subtitle = $get('subtitle', $get('description', 'Pronađite brze odgovore na najčešća pitanja o našoj platformi.'));
    $items = $data['items'] ?? [];

    $style = $data['style'] ?? [];
    $widthClass = $style['width'] ?? 'max-w-4xl';
    $minHeight = ($style['min_height'] ?? 'auto') === 'auto' ? '' : ($style['min_height'] ?? '');
    $paddingY = $style['padding_y'] ?? 'py-space-12';
    $rounded = $style['rounded'] ?? 'rounded-2xl';
    $align = $style['text_align'] ?? 'text-center';
@endphp

@if(!empty($items))
<section class="mx-auto px-gutter-md sm:px-gutter-lg w-full {{ $widthClass }} {{ $minHeight }} {{ $paddingY }}" id="faq">
    <div class="{{ $align }} max-w-3xl mx-auto mb-space-8 sm:mb-space-10">
        @if($sectionTag)
            <span class="font-label-xs text-label-xs text-primary uppercase tracking-widest font-semibold bg-surface-container px-3 py-1 rounded-full">
                {{ $sectionTag }}
            </span>
        @endif
        @if($heading)
            <h2 class="text-2xl sm:text-3xl lg:text-4xl text-on-surface font-bold mt-space-3 tracking-tight break-words">
                {{ $heading }}
            </h2>
        @endif
        @if($subtitle)
            <p class="font-body-md text-body-md text-on-surface-variant mt-2 sm:mt-3 max-w-2xl mx-auto leading-relaxed text-sm sm:text-base">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    <div class="max-w-3xl mx-auto space-y-3 sm:space-y-4">
        @foreach($items as $idx => $item)
            @php
                $q = is_array($item['question'] ?? null) 
                    ? ($item['question'][$locale] ?? $item['question']['sr'] ?? $item['question']['en'] ?? '') 
                    : ($item['question'] ?? '');
                $a = is_array($item['answer'] ?? null) 
                    ? ($item['answer'][$locale] ?? $item['answer']['sr'] ?? $item['answer']['en'] ?? '') 
                    : ($item['answer'] ?? '');
            @endphp
            <div x-data="{ open: false }" 
                 class="{{ $rounded }} bg-surface-container-lowest border border-surface-container-high/60 shadow-sm transition-all overflow-hidden">
                <button @click="open = !open" 
                        type="button" 
                        class="w-full px-4 sm:px-6 py-3.5 sm:py-5 text-left flex items-center justify-between gap-3 sm:gap-4 font-label-md text-label-md font-semibold text-on-surface hover:text-primary transition-colors focus:outline-none">
                    <span class="text-base sm:text-lg break-words">{{ $q }}</span>
                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-surface-container flex items-center justify-center flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180 bg-primary-container text-on-primary-container': open }">
                        <span class="material-symbols-outlined text-sm sm:text-base">expand_more</span>
                    </span>
                </button>
                <div x-show="open" 
                     x-collapse 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-4 sm:px-6 pb-4 sm:pb-6 pt-2 text-on-surface-variant font-body-md text-body-md leading-relaxed border-t border-surface-container/50 text-sm sm:text-base">
                    {!! nl2br(e($a)) !!}
                </div>
            </div>
        @endforeach
    </div>
</section>

@php
    $schemaFaq = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($items)->map(function ($item) use ($locale) {
            $q = is_array($item['question'] ?? null) 
                ? ($item['question'][$locale] ?? $item['question']['sr'] ?? $item['question']['en'] ?? '') 
                : ($item['question'] ?? '');
            $a = is_array($item['answer'] ?? null) 
                ? ($item['answer'][$locale] ?? $item['answer']['sr'] ?? $item['answer']['en'] ?? '') 
                : ($item['answer'] ?? '');
            return [
                '@type' => 'Question',
                'name' => trim(strip_tags((string) $q)),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) $a)),
                ],
            ];
        })->filter(fn ($item) => !empty($item['name']) && !empty($item['acceptedAnswer']['text']))->values()->all(),
    ];
@endphp

@if(!empty($schemaFaq['mainEntity']))
<script type="application/ld+json">
{!! json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endif
