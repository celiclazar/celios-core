@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $sectionTag = $get('section_tag', 'Arhitektura & Mogućnosti');
    $sectionTitle = $get('section_title', 'Zašto programeri i dizajneri biraju Celios');
    $sectionDesc = $get('section_desc', 'Bez komplikovanih eksternih servisa ili glomaznih dodataka. Sve je napisano u modernom Laravelu prateći najviše inženjerske i vizuelne standarde.');

    $cards = $data['cards'] ?? [
        [
            'icon' => 'translate',
            'title' => ['sr' => 'Multi-Language iz kutije', 'en' => 'Multi-Language Out of the Box'],
            'description' => [
                'sr' => 'Sinhronizovano upravljanje SR/EN tekstovima bez sporih eksternih pluginova. Podrška za fallback prevode, nezavisne slug-ove po jeziku i automatske hreflang tagove za pretraživače.',
                'en' => 'Synchronized multilingual SR/EN management without bulky plugins. Native fallback translations, language-specific slugs, and automated hreflang meta tags for search engines.'
            ],
            'checklist' => [
                'sr' => "Latinica i ćirilica podržani\nNema SQL dupliranja strukture",
                'en' => "Latin and Cyrillic supported\nNo SQL duplicate schemas"
            ]
        ],
        [
            'icon' => 'speed',
            'title' => ['sr' => 'Filament v3 Reaktivnost', 'en' => 'Filament v3 Reactivity'],
            'description' => [
                'sr' => 'Korišćenje najnaprednijeg admin ekosistema za PHP. Modularni resursi, brze tabele sa serverskim sortiranjem i filtriranjem, prilagođeni grafikoni i uloge u samo par linija koda.',
                'en' => 'Leveraging the premier PHP administrative framework. Modular resources, high-velocity tables with server-side sorting, interactive charts, and granular roles in minimal code.'
            ],
            'checklist' => [
                'sr' => "Livewire 3 reaktivnost\nAutomatski CSRF i Shield RBAC",
                'en' => "Livewire 3 reactivity\nAutomated CSRF & Shield RBAC"
            ]
        ],
        [
            'icon' => 'widgets',
            'title' => ['sr' => 'Fleksibilni Layout Builder', 'en' => 'Flexible Layout Builder'],
            'description' => [
                'sr' => 'Slaganje sekcija bez nereda u kodu i bez ograničenja. Svaki blok je izolovani Blade template koji dizajneri mogu stilizovati Tailwind klasama direktno u repozitorijumu.',
                'en' => 'Compose rich page layouts without messy code or restrictions. Each block is an isolated Blade template easily styled with Tailwind classes in your project repo.'
            ],
            'checklist' => [
                'sr' => "Drag-and-drop sortiranje\nVerzionisanje i istorijat izmena",
                'en' => "Drag-and-drop sorting\nRevisions and change history"
            ]
        ]
    ];

    $style = $data['style'] ?? [];
    $widthClass = $style['width'] ?? 'max-w-7xl';
    $minHeight = ($style['min_height'] ?? 'auto') === 'auto' ? '' : ($style['min_height'] ?? '');
    $paddingY = $style['padding_y'] ?? 'py-space-12';
    $rounded = $style['rounded'] ?? 'rounded-3xl';
    $align = $style['text_align'] ?? 'text-center';
    $cardStyle = ($style['card_style'] ?? 'card') === 'flat' ? 'bg-transparent border-0 shadow-none' : 'bg-surface-container-lowest border border-surface-container-high/60 shadow-sm hover:shadow-md';
@endphp

<!-- INFO O CMS-u: MOGUĆNOSTI I ARHITEKTURA -->
<section class="mx-auto px-gutter-md sm:px-gutter-lg w-full {{ $widthClass }} {{ $minHeight }} {{ $paddingY }}" id="mogucnosti">
    <div class="{{ $align }} max-w-3xl mx-auto mb-space-8 sm:mb-space-12">
        @if($sectionTag)
            <span class="inline-block font-label-xs text-label-xs text-primary uppercase tracking-widest font-semibold bg-surface-container px-3 py-1 rounded-full">
                {{ $sectionTag }}
            </span>
        @endif
        <h2 class="text-2xl sm:text-3xl lg:text-4xl text-on-surface font-bold mt-space-3 tracking-tight break-words">
            {{ $sectionTitle }}
        </h2>
        @if($sectionDesc)
            <p class="font-body-md text-body-md text-on-surface-variant mt-3 max-w-2xl mx-auto leading-relaxed">
                {{ $sectionDesc }}
            </p>
        @endif
    </div>

    <!-- Feature Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-space-6">
        @foreach($cards as $card)
            @php
                $cardTitle = is_array($card['title'] ?? null) ? ($card['title'][$locale] ?? $card['title']['sr'] ?? $card['title']['en'] ?? '') : ($card['title'] ?? '');
                $cardDesc = is_array($card['description'] ?? null) ? ($card['description'][$locale] ?? $card['description']['sr'] ?? $card['description']['en'] ?? '') : ($card['description'] ?? '');
                $cardChecklist = is_array($card['checklist'] ?? null) ? ($card['checklist'][$locale] ?? $card['checklist']['sr'] ?? $card['checklist']['en'] ?? '') : ($card['checklist'] ?? '');
                $checklistItems = is_string($cardChecklist) ? array_filter(array_map('trim', explode("\n", $cardChecklist))) : (is_array($cardChecklist) ? $cardChecklist : []);
            @endphp
            <div class="{{ $rounded }} {{ $cardStyle }} p-5 sm:p-space-6 transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-4">
                        <span class="material-symbols-outlined text-2xl">{{ $card['icon'] ?? 'widgets' }}</span>
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
