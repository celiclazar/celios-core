@php
    $locale = app()->getLocale();

    if (isset($section) && $section instanceof \Celios\Core\Models\Section) {
        $layout = $section->layout ?? '1_col';
        $style = $section->style ?? [];
        $header = $section->header ?? [];
        $columns = $section->columns ?? [];
        $bgClasses = $section->getBackgroundClasses();
        $padClasses = $section->getPaddingClasses();
        $maxWidthClasses = $section->getMaxWidthClasses();
        $gapClasses = $section->getGapClasses();
        $itemsAlignClasses = $section->getItemsAlignClasses();
        $gridClasses = $section->getGridClasses();
    } else {
        $data = $data ?? [];
        $layout = $data['layout'] ?? '1_col';
        $style = $data['style'] ?? [];
        $header = $data['header'] ?? [];
        $columns = $data['columns'] ?? [];

        $bg = $style['background'] ?? 'default';
        $bgClasses = match ($bg) {
            'surface_low' => 'bg-surface-container-low/70 border-y border-surface-container-high/40',
            'surface_lowest' => 'bg-surface-container-lowest shadow-sm border-y border-surface-container-high/30',
            'primary_tint' => 'bg-primary/[0.04] border-y border-primary/10',
            'dark' => 'bg-slate-900 text-white',
            'brand' => 'bg-primary text-on-primary',
            default => '',
        };

        $pad = $style['padding_y'] ?? 'md';
        $padClasses = match ($pad) {
            'none' => 'py-0',
            'sm' => 'py-6 sm:py-8',
            'lg' => 'py-16 sm:py-24',
            'xl' => 'py-20 sm:py-32',
            default => 'py-10 sm:py-16',
        };

        $width = $style['max_width'] ?? 'default';
        $maxWidthClasses = match ($width) {
            'narrow' => 'max-w-4xl',
            'full' => 'w-full max-w-none',
            default => 'max-w-7xl',
        };

        $gap = $style['gap'] ?? 'md';
        $gapClasses = match ($gap) {
            'none' => 'gap-0',
            'sm' => 'gap-4 sm:gap-6',
            'lg' => 'gap-8 sm:gap-12',
            default => 'gap-6 sm:gap-8',
        };

        $align = $style['items_align'] ?? 'top';
        $itemsAlignClasses = match ($align) {
            'center' => 'items-center',
            'bottom' => 'items-end',
            'stretch' => 'items-stretch',
            default => 'items-start',
        };

        $gridClasses = match ($layout) {
            '2_cols' => 'grid grid-cols-1 md:grid-cols-2',
            '3_cols' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
            '4_cols' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
            '1_2_cols', '2_1_cols' => 'grid grid-cols-1 md:grid-cols-3',
            default => 'grid grid-cols-1',
        };
    }

    $badge = is_array($header['badge'] ?? null)
        ? ($header['badge'][$locale] ?? $header['badge'][config('locales.default', 'sr')] ?? $header['badge']['en'] ?? '')
        : (string) ($header['badge'] ?? '');

    $title = is_array($header['title'] ?? null)
        ? ($header['title'][$locale] ?? $header['title'][config('locales.default', 'sr')] ?? $header['title']['en'] ?? '')
        : (string) ($header['title'] ?? '');

    $subtitle = is_array($header['subtitle'] ?? null)
        ? ($header['subtitle'][$locale] ?? $header['subtitle'][config('locales.default', 'sr')] ?? $header['subtitle']['en'] ?? '')
        : (string) ($header['subtitle'] ?? '');

    $headerAlign = ($header['align'] ?? 'left') === 'center' ? 'text-center mx-auto' : 'text-left';

    $blockModuleMap = [
        'posts_block' => 'blog',
        'documents_block' => 'documents',
        'form_block' => 'forms',
        'newsletter' => 'newsletter',
    ];
@endphp

<section class="w-full relative transition-colors duration-200 {{ $bgClasses }} {{ $padClasses }}">
    <div class="mx-auto px-gutter-md sm:px-gutter-lg {{ $maxWidthClasses }}">
        @if(!empty($badge) || !empty($title) || !empty($subtitle))
            <div class="mb-8 sm:mb-12 max-w-3xl {{ $headerAlign }}">
                @if(!empty($badge))
                    <span class="inline-block px-3 py-1 mb-3 text-xs font-semibold tracking-wider uppercase rounded-full bg-primary/10 text-primary">
                        {{ $badge }}
                    </span>
                @endif
                @if(!empty($title))
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-on-surface">
                        {{ $title }}
                    </h2>
                @endif
                @if(!empty($subtitle))
                    <p class="mt-3 text-base sm:text-lg text-on-surface-variant font-body-md leading-relaxed">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        @if(!empty($columns) && is_array($columns))
            <div class="{{ $gridClasses }} {{ $gapClasses }} {{ $itemsAlignClasses }}">
                @foreach($columns as $colIndex => $col)
                    @php
                        $spanClass = 'w-full';
                        if ($layout === '1_2_cols') {
                            $spanClass = $colIndex === 0 ? 'md:col-span-1 w-full' : 'md:col-span-2 w-full';
                        } elseif ($layout === '2_1_cols') {
                            $spanClass = $colIndex === 0 ? 'md:col-span-2 w-full' : 'md:col-span-1 w-full';
                        }
                        $colBlocks = $col['blocks'] ?? [];
                    @endphp
                    <div class="{{ $spanClass }} flex flex-col gap-4">
                        @if(is_array($colBlocks))
                            @foreach($colBlocks as $nestedBlock)
                                @php
                                    $bType = $nestedBlock['type'] ?? '';
                                    $bData = $nestedBlock['data'] ?? [];
                                    $reqMod = $blockModuleMap[$bType] ?? null;
                                @endphp
                                @if(!$reqMod || module_enabled($reqMod))
                                    @includeIf('components.blocks.' . $bType, [
                                        'data' => $bData,
                                        'inSection' => true,
                                    ])
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
