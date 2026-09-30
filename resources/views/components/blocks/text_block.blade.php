@php
    $locale = app()->getLocale();
    $body = $data['body'][$locale] ?? $data['body'][config('locales.default', 'sr')] ?? (is_string($data['body'] ?? null) ? $data['body'] : '');
    $style = $data['style'] ?? [];
    $widthClass = $style['width'] ?? 'max-w-4xl';
    $minHeight = ($style['min_height'] ?? 'auto') === 'auto' ? '' : ($style['min_height'] ?? '');
    $paddingY = $style['padding_y'] ?? 'py-space-12';
    $rounded = $style['rounded'] ?? 'rounded-3xl';
    $align = $style['text_align'] ?? 'text-left';
    $cardStyle = ($style['card_style'] ?? 'card') === 'flat' ? 'bg-transparent border-0 shadow-none p-0' : 'bg-surface-container-lowest border border-surface-container-high/60 p-5 sm:p-space-8 md:p-space-12 shadow-sm';
@endphp

@if(!empty($body))
<section class="px-gutter-md sm:px-gutter-lg mx-auto w-full {{ $widthClass }} {{ $minHeight }} {{ $paddingY }}">
    <div class="w-full {{ $rounded }} {{ $align }} {{ $cardStyle }} prose prose-slate max-w-none text-on-surface font-body-md leading-relaxed break-words overflow-hidden">
        {!! $body !!}
    </div>
</section>
@endif
