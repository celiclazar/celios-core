@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $sectionTag = $get('section_tag', 'Realizovani Projekti');
    $sectionTitle = $get('section_title', 'Izrađeno u Celios CMS-u');
    $sectionDesc = $get('section_desc', 'Odabrani digitalni prostori, portfoliji i publikacije lansirani na našoj platformi.');
    $countLabel = $get('count_label', 'Prikazano: 4 od 28 projekata');

    $projects = $data['projects'] ?? [
        [
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCKpBc5Pvv1WS3DmXJljzhXspNg4afU4UIzaQAWGNSH62ErCfJNghI2WNVAE4g93YO0d1ZbSNRu0b-Uy81LbBj6EOGVbrIaBD6AXAyqCnkDFbB2fZwAYhOTeikhsnpvvzGEouSDa8Nn34wdOYdxak32Xl-IjLOF0Sh9VGn_GQZfrE-rhFcWrM--n6yKAd3UGfjW9I8alYLTh08xUT8Mj-0kXUCsHNH0cvtdbnlfOCNtptUuIMW3nx0S',
            'client_year' => 'Studio Forma • 2024',
            'category' => 'Arhitektura',
            'languages_badge' => 'SR / EN',
            'title' => [
                'sr' => 'Studio Forma: Monografija i Portfolio Radova',
                'en' => 'Studio Forma: Monograph & Architectural Portfolio'
            ],
            'description' => [
                'sr' => 'Interaktivni katalog sa fluidnim prelazima strana, prilagođenim layout blokovima za vertikalne fotografije i dinamičkim filtriranjem po tipologiji objekta.',
                'en' => 'Interactive monograph with fluid page transitions, tailored vertical image layout blocks, and dynamic typology filtering.'
            ],
            'tags' => 'Laravel 11, Filament v3, Alpine.js',
            'case_study_note' => [
                'sr' => 'Studio Forma — 100/100 Core Web Vitals i 0.2s prelaz stranica.',
                'en' => 'Studio Forma — 100/100 Core Web Vitals and 0.2s page transitions.'
            ]
        ],
        [
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC6QUDF-ZKPm8AWKSvTj1n0cMX3wlr9B3XIDIjXySacPKow_eQcv_3N8YFKAQXFbpW6Q1CecBu9y1REHeXeVzGgqj9U5jUzbCJITIhVD8iF7P717cSqAwi6YU3Qt6IQUVTeyhCAAcDTqUC3hXagGnyPQ1Xa2mONntzEUvaIlcYCripqsDqr0H25ffiA4IlbqxGBX-1Ji-BHNOLBYKU5yXyjemuwbFmgOOosydvqwMjR5krdiPF5VVso',
            'client_year' => 'Grafički Salon • 2024',
            'category' => 'Kultura',
            'languages_badge' => 'SRB',
            'title' => [
                'sr' => 'Tipografski Glasnik: Arhiv Jugoslovenskog Plakata',
                'en' => 'Typographic Gazette: Yugoslav Poster Archive'
            ],
            'description' => [
                'sr' => 'Digitalizacija istorijske građe sa detaljnim metapodacima o fontovima, autorima i tehnikama štampe, potpuno pretraživo uz Celios Full-Text Search.',
                'en' => 'Digital archiving of historical artifacts with typography metadata, author indexing, and instant full-text filtering.'
            ],
            'tags' => 'Tailwind CSS, Livewire 3, PostgreSQL',
            'case_study_note' => [
                'sr' => 'Tipografski Glasnik — Preko 3,800 skeniranih artefakata sa brzim pretragama.',
                'en' => 'Typographic Gazette — Over 3,800 digitized posters with fast search.'
            ]
        ],
        [
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDM7cwuNMt1cLKfctMWOjAGN7CSVZ7DGKjft3UyEDATErB-ERhFG4Kdw96FYfjeENAVTopeDKJgoipK22q4IPPW2qfiac5cY3x37W1BIWHwHy9KjnzZVYLj3ppjwTyu4AHGvLCWOrQsmWHWAwX6bsrJzFYf8ZA7yBoTWDN5Ps5OSPmq-rxP4NYPKmDYnAACHsNIcv6ft3fx7CJVZkFRSmN80PIRjo3cN0gsBiyv14bKdH5g0Qgn2AC_',
            'client_year' => 'FinFlow Global • 2023/24',
            'category' => 'Fintech',
            'languages_badge' => 'EN / DE',
            'title' => [
                'sr' => 'FinFlow: Korporativni Portal & Dokumentacija',
                'en' => 'FinFlow: Corporate Portal & API Docs'
            ],
            'description' => [
                'sr' => 'Sinhronizovani headless blog sa dokumentacionim podsistemom, podrškom za više autora sa granularnim nivoima pristupa kroz Filament uloge.',
                'en' => 'Synchronized headless publication hub and docs subsystem with granular team authorizations via Filament Shield.'
            ],
            'tags' => 'Laravel API, Meilisearch, Filament Shield',
            'case_study_note' => [
                'sr' => 'FinFlow — Multi-author headless publishing workflow.',
                'en' => 'FinFlow — Multi-author headless publishing workflow.'
            ]
        ],
        [
            'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBS1LDKr-W-BIf5nmmb-tzNC97wRXZPof9uDw47iPHvzPnjqmEqa9OldaMlxkpFI_G6RPsc_D_7UPaRhwiqaWlwfRUeSb3YRQ611Gwss3PcxT22yO2ZbU09M3ZpE_Y3nqnhTDxH3_2m_Iw6snJKGuoxTl6tbiUk8pPgIB88ady7eiPJmtc7IRxyENQ0bx3HsGjI524HjaYT2GiINk_ya6whYj-VjFeQEqOFMPAs6pRgn1Jt64pTLrVQ',
            'client_year' => 'Glinica Studio • 2024',
            'category' => 'E-Commerce',
            'languages_badge' => 'SR / EN',
            'title' => [
                'sr' => 'Glinica: Zanatska Radionica i Online Katalog',
                'en' => 'Glinica: Artisan Ceramic Studio & Online Store'
            ],
            'description' => [
                'sr' => 'Hibridni portfolio koji kombinuje priču o zanatu sa real-time stanjem lagera, integrisanim plaćanjem i modularnim karticama za prijavu na radionice.',
                'en' => 'Hybrid showcase balancing artisan storytelling with real-time stock availability, seamless checkout, and workshop booking blocks.'
            ],
            'tags' => 'Laravel Cashier, Spatie Media, Filament v3',
            'case_study_note' => [
                'sr' => 'Glinica — Kombinacija zanatskog brendinga i e-commerce checkout-a.',
                'en' => 'Glinica — Artisan storytelling coupled with swift e-commerce checkout.'
            ]
        ]
    ];
@endphp

<!-- REALIZOVANI PROJEKTI / PORTFOLIO -->
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="portfolio">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-space-4 mb-space-6 sm:mb-space-8">
        <div>
            @if($sectionTag)
                <div class="flex items-center gap-space-2 text-primary font-label-xs text-label-xs uppercase tracking-widest font-semibold mb-space-1">
                    <span class="material-symbols-outlined text-base">work</span>
                    <span>{{ $sectionTag }}</span>
                </div>
            @endif
            <h2 class="text-2xl sm:text-3xl lg:text-4xl text-on-surface font-bold tracking-tight break-words">
                {{ $sectionTitle }}
            </h2>
            @if($sectionDesc)
                <p class="font-body-md text-body-md text-on-surface-variant mt-1 leading-relaxed">
                    {{ $sectionDesc }}
                </p>
            @endif
        </div>
        @if($countLabel)
            <div class="flex items-center gap-space-2 shrink-0">
                <span class="font-label-xs text-label-xs text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">
                    {{ $countLabel }}
                </span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-space-6">
        @foreach($projects as $project)
            @php
                $pTitle = is_array($project['title'] ?? null) ? ($project['title'][$locale] ?? $project['title']['sr'] ?? $project['title']['en'] ?? '') : ($project['title'] ?? '');
                $pDesc = is_array($project['description'] ?? null) ? ($project['description'][$locale] ?? $project['description']['sr'] ?? $project['description']['en'] ?? '') : ($project['description'] ?? '');
                $pNote = is_array($project['case_study_note'] ?? null) ? ($project['case_study_note'][$locale] ?? $project['case_study_note']['sr'] ?? $project['case_study_note']['en'] ?? '') : ($project['case_study_note'] ?? '');
                $tags = is_string($project['tags'] ?? '') ? array_filter(array_map('trim', explode(',', $project['tags']))) : (is_array($project['tags'] ?? []) ? $project['tags'] : []);

                $defaultUrls = [
                    0 => url("/{$locale}/studio-forma"),
                    1 => url("/{$locale}/tipografski-glasnik"),
                    2 => url("/{$locale}/finflow-portal"),
                    3 => url("/{$locale}/glinica-studio"),
                ];
                $projectUrl = !empty($project['project_url']) 
                    ? (str_starts_with($project['project_url'], 'http') || str_starts_with($project['project_url'], '/') ? $project['project_url'] : url("/{$locale}/" . $project['project_url']))
                    : ($defaultUrls[$loop->index] ?? '#');
            @endphp
            <div class="group rounded-2xl bg-surface-container-lowest p-4 sm:p-space-5 border border-surface-container-high/60 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <a href="{{ $projectUrl }}" class="block rounded-xl overflow-hidden h-48 sm:h-60 bg-surface-container relative">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $pTitle }}" src="{{ $project['image_url'] ?? '' }}"/>
                        <div class="absolute top-3 right-3 flex gap-1">
                            @if(!empty($project['languages_badge']))
                                <span class="font-label-xs text-label-xs bg-surface-container-lowest/90 backdrop-blur-sm text-on-surface px-space-2 py-0.5 rounded font-semibold shadow-sm">
                                    {{ $project['languages_badge'] }}
                                </span>
                            @endif
                            @if(!empty($project['category']))
                                <span class="font-label-xs text-label-xs bg-primary-fixed text-on-primary-fixed px-space-2 py-0.5 rounded font-semibold">
                                    {{ $project['category'] }}
                                </span>
                            @endif
                        </div>
                    </a>
                    <div class="pt-space-4">
                        @if(!empty($project['client_year']))
                            <div class="flex items-center gap-space-2 text-outline font-label-xs text-label-xs uppercase tracking-wider mb-1">
                                <span>{{ $project['client_year'] }}</span>
                            </div>
                        @endif
                        <h3 class="text-lg sm:text-xl font-semibold text-on-surface group-hover:text-primary transition-colors break-words">
                            <a href="{{ $projectUrl }}" class="hover:underline">
                                {{ $pTitle }}
                            </a>
                        </h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-1 leading-relaxed break-words">
                            {{ $pDesc }}
                        </p>
                    </div>
                </div>
                <div class="pt-space-4 mt-space-4 border-t border-surface-container flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-1">
                        @foreach($tags as $t)
                            <span class="font-code-sm text-code-sm bg-surface-container px-space-2 py-0.5 rounded text-on-surface-variant">
                                {{ $t }}
                            </span>
                        @endforeach
                    </div>
                    <a href="{{ $projectUrl }}" class="inline-flex items-center gap-1 text-primary font-label-sm text-label-sm font-semibold hover:underline shrink-0">
                        <span>Pogledaj Case Study</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>
