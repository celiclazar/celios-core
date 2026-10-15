@php
    $locale = app()->getLocale();
    $rawLabel = $data['label'] ?? '';
    $label = is_array($rawLabel)
        ? ($rawLabel[$locale] ?? $rawLabel[config('locales.default', 'sr')] ?? $rawLabel['en'] ?? '')
        : (string) $rawLabel;

    $url = $data['url'] ?? '#';
    $variant = $data['variant'] ?? 'primary';
    $size = $data['size'] ?? 'md';
    $align = $data['align'] ?? 'left';
    $icon = $data['icon'] ?? 'arrow-right';
    $openInNewTab = !empty($data['open_in_new_tab']);

    $variantClasses = match ($variant) {
        'secondary' => 'bg-surface-container-high text-on-surface hover:bg-surface-container-highest active:scale-[0.98]',
        'outline' => 'border border-surface-container-highest text-on-surface hover:bg-surface-container-low active:scale-[0.98]',
        'dark' => 'bg-slate-900 text-white hover:bg-slate-800 shadow-sm active:scale-[0.98]',
        'accent' => 'bg-gradient-to-r from-primary to-accent text-white hover:opacity-95 shadow-md active:scale-[0.98]',
        default => 'bg-primary text-on-primary hover:bg-primary/90 shadow-sm active:scale-[0.98]',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3.5 py-1.5 text-xs rounded-full gap-1.5',
        'lg' => 'px-6 py-3.5 text-base font-semibold rounded-full gap-2.5',
        default => 'px-5 py-2.5 text-sm font-semibold rounded-full gap-2',
    };

    $alignClasses = match ($align) {
        'center' => 'justify-center',
        'right' => 'justify-end',
        'stretch' => 'w-full [&>a]:w-full [&>a]:justify-center',
        default => 'justify-start',
    };
@endphp

@if(!empty($label))
<div class="flex {{ $alignClasses }} w-full my-2">
    <a href="{{ $url }}"
       @if($openInNewTab) target="_blank" rel="noopener noreferrer" @endif
       class="group inline-flex items-center transition-all duration-200 {{ $sizeClasses }} {{ $variantClasses }}">
        <span>{{ $label }}</span>
        @if($icon === 'arrow-right')
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        @elseif($icon === 'external-link')
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        @elseif($icon === 'sparkles')
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
        @elseif($icon === 'download')
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        @elseif($icon === 'check')
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        @endif
    </a>
</div>
@endif
