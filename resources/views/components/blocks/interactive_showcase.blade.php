@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $sectionTag = $get('section_tag', 'Fleksibilni Graditelj Stranica');
    $sectionTitle = $get('section_title', 'Modularni Blokovi u Akciji');
    $sectionDesc = $get('section_desc', 'Odaberite stil prikaza i testirajte ponašanje sekcija na radnoj površini sa dinamičkom dvojezičnom proverom.');

    // Split Tab
    $splitTag = $get('split_tag', 'Block: Architecture & Space Showcase');
    $splitTitle = $get('split_title', 'Precizna estetika za moderne kreativne studije.');
    $splitDesc = $get('split_desc', 'Svi blokovi poseduju direktne opcije konfiguracije kroz Filament panel: izmene margina, tipografskih razmera, relacija sa slikama iz biblioteke i SEO meta oznakama na nivou fragmenta.');
    $splitBtnText = $get('split_btn_text', 'Pogledaj Arhivu Radova');
    $splitProjectTitle = $get('split_project_title', 'Atelje Beograd • Monolit 24');
    $splitImg = $data['split_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDkf9ewQzU8QrKp-qRL56pWjwkvGuD7ubhnCzQG2lQ3cXK8OeVT2DiWye2s7qUBbEpzkdwuSKViMomNK6UMezxrtp0V39vILCEAlop2DbKYEqJhjp5zH41YDtd6mFFXMGXsdp7MhUTccnlIq819Veu-nQUBPgVw1lWsbzSPjVOsKqVbJF6iXKQhmc4fGdGIhNsaWVSfUgRTRzaL2HS4cv1TOla65PFv9nAEPAdAkskWyrbgn4Lmux9V';

    // Bento Tab
    $bento1Title = $get('bento_1_title', 'Interaktivni Digitalni Arhiv');
    $bento1Desc = $get('bento_1_desc', 'Strukturirana baza eksponata sa više od 4,000 unosa, asinhronim pretraživanjem i automatskim generisanjem OpenGraph slika.');
    $bento1Img = $data['bento_1_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBgPZGNZyyWgfr6P7GSDV7tl2GrapFX1nw2u_tHypcpspgbcZ7cXXyhWfwFkPOzgDJ10E_ECXgCihBN5WucWOorYScmJjV_ORJHQrGhm9g4cVHk1w14lK1OgZqYl6M_kCTa7yMuxAIHT_AMuGLYN1V-95dGLW52HftxK3siFl1V7uvSnFJEn4Q9fZOuRXA06WWKyQ7dI3NsP8kgNKV8Zv5cxsQjVnS3L4Da-uwr97ZCnOu3h3pFn0xh';

    // Case Studies Tab
    $case1Title = $get('case_1_title', 'Nordic Furniture Lab — Digitalni Salon 2024');
    $case1Desc = $get('case_1_desc', 'Kako smo postigli 300% brži odziv kataloga sa preko 1,200 3D modela koristeći Celios blokove.');
    $case1Img = $data['case_1_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlIynnFgBMnTnaqqupl5wwvbzkGlFY-9ZPFJw3V-F0tVuEu3dgff_ECLLyurfGYcG0FRqQGv7i2zQD5LB3Wbn7OY6ceDL_PyhHGAmRWcQiR1exm15bvFPVhtm53ldv5cuExy3IhO7eMW81ixd3VkFlk9CukXirLsNLDk281T2foV93db1sySxIIVBJXwLzEuzhAeShK3rp9ILkiorP91QMkV_A0Vx8vDkUDMqD56-MqGuHQXIzxMJJ';

    $case2Title = $get('case_2_title', 'Kvantum Branding Hub — Multi-tenant sajt');
    $case2Desc = $get('case_2_desc', 'Jedinstvena CMS instanca koja u realnom vremenu opslužuje 6 nezavisnih portfolio domena.');
    $case2Img = $data['case_2_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuAPgH8jA1mFcORzMpMwVjE_Hrc4c2bY62pDD2tjTxmRJPajDsbiRtUJX_kuw0bPWe72862DGNwQxE-0WXeTpEUserPwCLTYob1DRbKt1Vz6RcuRd-gjzyCL4UtA8xgiIgUr0P5Rzre6n_YAKbn7su0ayJj1Lf7ChkvPgKSbaOnRRGa7B-MhuwEcw4bYv5oXaNHCuI0rvbEtCDgs3gWAzPEBf7oI9PVDbO42v8O1gvm6a1YhwBlqHv83';

    // Build dynamic langData array for client side tab preview language switching
    $langDataMap = [
        'sr' => [
            'split_tag' => $data['split_tag']['sr'] ?? 'Blok: Arhitektura & Prostorni Prikaz',
            'split_title' => $data['split_title']['sr'] ?? 'Precizna estetika za moderne kreativne studije.',
            'split_desc' => $data['split_desc']['sr'] ?? 'Svi blokovi poseduju direktne opcije konfiguracije kroz Filament panel: izmene margina, tipografskih razmera, relacija sa slikama iz biblioteke i SEO meta oznakama na nivou fragmenta.',
            'bento_1_title' => $data['bento_1_title']['sr'] ?? 'Interaktivni Digitalni Arhiv',
            'bento_1_desc' => $data['bento_1_desc']['sr'] ?? 'Strukturirana baza eksponata sa više od 4,000 unosa, asinhronim pretraživanjem i automatskim generisanjem OpenGraph slika.',
        ],
        'en' => [
            'split_tag' => $data['split_tag']['en'] ?? 'Block: Architecture & Spatial Showcase',
            'split_title' => $data['split_title']['en'] ?? 'Precise aesthetics tailored for modern creative studios.',
            'split_desc' => $data['split_desc']['en'] ?? 'All content blocks provide immediate configuration options in Filament: margin tweaks, responsive typographic scaling, direct asset library relations, and fragment-level SEO tags.',
            'bento_1_title' => $data['bento_1_title']['en'] ?? 'Interactive Digital Archive',
            'bento_1_desc' => $data['bento_1_desc']['en'] ?? 'Structured exhibit repository boasting over 4,000 entries, asynchronous query filtering, and automated OpenGraph image generation.',
        ],
    ];
@endphp

<!-- INTERAKTIVNI BLOKOVI & LAYOUTI U AKCIJI -->
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="modularni-blokovi">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-4 mb-space-6">
        <div>
            @if($sectionTag)
                <div class="flex items-center gap-space-2 text-primary font-label-xs text-label-xs uppercase tracking-widest font-semibold mb-space-1">
                    <span class="material-symbols-outlined text-base">layers</span>
                    <span>{{ $sectionTag }}</span>
                </div>
            @endif
            <h2 class="text-2xl sm:text-3xl text-on-surface font-bold tracking-tight break-words">
                {{ $sectionTitle }}
            </h2>
            @if($sectionDesc)
                <p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-2xl leading-relaxed">
                    {{ $sectionDesc }}
                </p>
            @endif
        </div>

        <!-- Interactive Layout Controls -->
        <div class="flex items-center gap-1.5 sm:gap-space-2 bg-surface-container-low p-1.5 rounded-xl border border-surface-container-high/60 shadow-sm overflow-x-auto max-w-full scrollbar-none">
            <button class="layout-tab-btn px-2.5 sm:px-space-3 py-1.5 rounded-lg text-label-xs sm:text-label-sm font-semibold transition-all bg-surface-container-lowest text-on-surface shadow-sm shrink-0" id="btn-tab-split" onclick="switchLayoutTab('split')">
                Hero Minimal Split
            </button>
            <button class="layout-tab-btn px-2.5 sm:px-space-3 py-1.5 rounded-lg text-label-xs sm:text-label-sm font-semibold transition-all text-on-surface-variant hover:text-on-surface shrink-0" id="btn-tab-bento" onclick="switchLayoutTab('bento')">
                Bento Portfolio Grid
            </button>
            <button class="layout-tab-btn px-2.5 sm:px-space-3 py-1.5 rounded-lg text-label-xs sm:text-label-sm font-semibold transition-all text-on-surface-variant hover:text-on-surface shrink-0" id="btn-tab-case" onclick="switchLayoutTab('case')">
                Case Study Feed
            </button>
            <button class="layout-tab-btn px-2.5 sm:px-space-3 py-1.5 rounded-lg text-label-xs sm:text-label-sm font-semibold transition-all text-on-surface-variant hover:text-on-surface shrink-0" id="btn-tab-widget" onclick="switchLayoutTab('widget')">
                Interactive Widget
            </button>
            <div class="h-5 w-px bg-outline-variant mx-1 shrink-0"></div>
            <div class="flex items-center bg-surface-container p-0.5 rounded-lg shrink-0">
                <button class="px-space-2 py-0.5 rounded-md font-label-xs text-label-xs bg-surface-container-lowest text-on-surface font-semibold shadow-sm" id="lang-sr-btn" onclick="switchPreviewLang('sr')">SR</button>
                <button class="px-space-2 py-0.5 rounded-md font-label-xs text-label-xs text-on-surface-variant font-semibold hover:text-on-surface" id="lang-en-btn" onclick="switchPreviewLang('en')">EN</button>
            </div>
        </div>
    </div>

    <!-- Interactive Preview Frame -->
    <div class="rounded-2xl bg-surface-container-lowest border border-surface-container-high/60 shadow-md p-4 sm:p-space-6 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0 pb-space-4 mb-space-6 bg-surface-container-low px-3 sm:px-space-4 py-2.5 rounded-xl">
            <div class="flex items-center gap-space-2 min-w-0">
                <span class="w-3 h-3 rounded-full bg-outline-variant/60 shrink-0"></span>
                <span class="w-3 h-3 rounded-full bg-outline-variant/60 shrink-0"></span>
                <span class="w-3 h-3 rounded-full bg-outline-variant/60 shrink-0"></span>
                <span class="font-code-sm text-code-sm text-on-surface-variant ml-space-2 truncate">celios://preview/canvas-block?layout=<span id="preview-slug">hero-minimal-split</span></span>
            </div>
            <div class="flex items-center gap-space-3 shrink-0 self-end sm:self-auto">
                <span class="inline-flex items-center gap-1 font-label-xs text-label-xs text-on-surface-variant">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Livewire Stream Sync
                </span>
                <button class="px-space-2.5 py-1 rounded bg-surface-container text-on-surface font-label-xs text-label-xs hover:bg-surface-container-high transition-colors cursor-pointer" onclick="randomizeThemeVariant()" type="button">
                    Promeni Šemu Akcenta
                </button>
            </div>
        </div>

        <!-- Dynamic Container 1: Hero Minimal Split -->
        <div class="layout-panel flex flex-col lg:flex-row items-center gap-6 sm:gap-space-8 py-space-2 sm:py-space-4" id="layout-split">
            <div class="flex-1 flex flex-col gap-space-4 w-full">
                <div class="inline-flex items-center gap-2 px-space-2.5 py-1 rounded bg-secondary-container text-on-secondary-container font-label-xs text-label-xs w-fit">
                    <span class="material-symbols-outlined text-sm">view_quilt</span>
                    <span data-lang-text-key="split_tag">{{ $splitTag }}</span>
                </div>
                <h3 class="text-2xl sm:text-3xl text-on-surface tracking-tight font-bold break-words" data-lang-text-key="split_title">
                    {{ $splitTitle }}
                </h3>
                <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed break-words" data-lang-text-key="split_desc">
                    {{ $splitDesc }}
                </p>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-space-4 pt-space-2">
                    <a class="px-space-4 py-2.5 rounded-lg bg-primary text-on-primary font-label-sm text-label-sm font-semibold shadow-sm hover:bg-primary-container transition-colors text-center" href="#portfolio">
                        {{ $splitBtnText }}
                    </a>
                    <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm justify-center sm:justify-start">
                        <span class="material-symbols-outlined text-base text-primary">verified</span>
                        <span>Optimizovano za Retina</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 w-full relative">
                <div class="rounded-xl overflow-hidden shadow-md bg-surface-container-high relative group">
                    <img class="w-full h-64 sm:h-80 object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $splitProjectTitle }}" src="{{ $splitImg }}"/>
                    <div class="absolute bottom-3 left-3 right-3 bg-surface-container-lowest/90 backdrop-blur-md p-space-3 rounded-lg flex items-center justify-between">
                        <div>
                            <span class="font-label-xs text-label-xs text-outline uppercase tracking-wider">Projekat 01</span>
                            <p class="font-label-sm text-label-sm font-semibold text-on-surface">{{ $splitProjectTitle }}</p>
                        </div>
                        <span class="font-label-xs text-label-xs bg-primary-fixed text-on-primary-fixed px-space-2 py-0.5 rounded font-semibold">2024</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Container 2: Bento Portfolio Grid -->
        <div class="layout-panel hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-space-4 py-space-2 sm:py-space-4" id="layout-bento">
            <div class="sm:col-span-2 md:col-span-2 rounded-xl bg-surface-container-low p-4 sm:p-space-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <span class="font-label-xs text-label-xs bg-surface-container-lowest px-space-2.5 py-1 rounded-full text-primary font-semibold">Glavni Showcase</span>
                    <h4 class="text-xl sm:text-2xl text-on-surface font-semibold mt-space-3 break-words" data-lang-text-key="bento_1_title">{{ $bento1Title }}</h4>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-1 max-w-md break-words" data-lang-text-key="bento_1_desc">{{ $bento1Desc }}</p>
                </div>
                <div class="mt-space-4 sm:mt-space-6 rounded-lg overflow-hidden h-36 sm:h-44 bg-surface-container">
                    <img class="w-full h-full object-cover" alt="{{ $bento1Title }}" src="{{ $bento1Img }}"/>
                </div>
            </div>
            <div class="rounded-xl bg-surface-container-low p-4 sm:p-space-6 flex flex-col justify-between">
                <div>
                    <span class="font-label-xs text-label-xs bg-surface-container-lowest px-space-2.5 py-1 rounded-full text-secondary font-semibold">Performanse</span>
                    <h4 class="text-lg sm:text-xl text-on-surface font-semibold mt-space-3">Zero-JS Hydration</h4>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-1">Stranice se renderuju na serveru sa minimalnim footprint-om na klijentu.</p>
                </div>
                <div class="pt-space-4 sm:pt-space-6">
                    <div class="flex items-center justify-between text-label-xs font-label-xs text-on-surface mb-1">
                        <span>Core Web Vitals</span>
                        <span class="font-bold text-primary">99.4%</span>
                    </div>
                    <div class="w-full bg-surface-container-highest rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full w-[99%]"></div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-surface-container-low p-4 sm:p-space-6">
                <div class="w-10 h-10 rounded-lg bg-surface-container-lowest flex items-center justify-center text-primary mb-space-3 shadow-sm">
                    <span class="material-symbols-outlined">translate</span>
                </div>
                <h4 class="text-lg text-on-surface font-semibold">Dvojezični Režim</h4>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-1">Podrška za ćirilicu, latinicu i engleski jezik direktno u bazi podataka bez tabela duplikata.</p>
            </div>
            <div class="sm:col-span-2 md:col-span-2 rounded-xl bg-surface-container-low p-4 sm:p-space-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="max-w-md">
                    <span class="font-label-xs text-label-xs text-outline uppercase tracking-wider">Modularni sistem</span>
                    <h4 class="text-base sm:text-lg text-on-surface font-semibold mt-1">Spreman za headless i hibridni rad</h4>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">REST i GraphQL krajnje tačke automatski generisane za svaki kreirani tip sadržaja.</p>
                </div>
                <button class="w-full sm:w-auto px-space-3 py-2 rounded-lg bg-surface-container-lowest text-on-surface font-label-sm text-label-sm font-semibold shadow-sm hover:bg-surface-container transition-colors cursor-pointer text-center" onclick="alert('API dokumentacija: Swagger / OpenAPI 3.1 rute su aktivne.')">
                    Pregled API-ja
                </button>
            </div>
        </div>

        <!-- Dynamic Container 3: Case Study Feed -->
        <div class="layout-panel hidden flex flex-col gap-3 sm:gap-space-4 py-space-2 sm:py-space-4" id="layout-case">
            <div class="p-3.5 sm:p-space-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-space-4">
                <div class="flex items-center gap-3 sm:gap-space-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg bg-surface-container-high overflow-hidden shrink-0">
                        <img class="w-full h-full object-cover" alt="{{ $case1Title }}" src="{{ $case1Img }}"/>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-space-2">
                            <span class="font-label-xs text-label-xs bg-surface-container-lowest text-primary px-space-2 py-0.5 rounded font-semibold">STUDIJA SLUČAJA #01</span>
                            <span class="font-label-xs text-label-xs text-outline">• 4 min čitanja</span>
                        </div>
                        <h4 class="text-sm sm:text-base text-on-surface font-semibold mt-1 break-words">{{ $case1Title }}</h4>
                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1 break-words">{{ $case1Desc }}</p>
                    </div>
                </div>
                <a class="w-full sm:w-auto text-center px-space-3 py-2 rounded-lg bg-surface-container-lowest text-primary hover:bg-primary hover:text-on-primary font-label-sm text-label-sm font-semibold shadow-sm transition-all whitespace-nowrap" href="#portfolio">
                    Pročitaj Detaljno
                </a>
            </div>
            <div class="p-3.5 sm:p-space-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-space-4">
                <div class="flex items-center gap-3 sm:gap-space-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg bg-surface-container-high overflow-hidden shrink-0">
                        <img class="w-full h-full object-cover" alt="{{ $case2Title }}" src="{{ $case2Img }}"/>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-space-2">
                            <span class="font-label-xs text-label-xs bg-surface-container-lowest text-tertiary px-space-2 py-0.5 rounded font-semibold">STUDIJA SLUČAJA #02</span>
                            <span class="font-label-xs text-label-xs text-outline">• 6 min čitanja</span>
                        </div>
                        <h4 class="text-sm sm:text-base text-on-surface font-semibold mt-1 break-words">{{ $case2Title }}</h4>
                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1 break-words">{{ $case2Desc }}</p>
                    </div>
                </div>
                <a class="w-full sm:w-auto text-center px-space-3 py-2 rounded-lg bg-surface-container-lowest text-primary hover:bg-primary hover:text-on-primary font-label-sm text-label-sm font-semibold shadow-sm transition-all whitespace-nowrap" href="#portfolio">
                    Pročitaj Detaljno
                </a>
            </div>
        </div>

        <!-- Dynamic Container 4: Interactive Widget -->
        <div class="layout-panel hidden py-space-2 sm:py-space-4" id="layout-widget">
            <div class="p-4 sm:p-space-6 rounded-xl bg-surface-container-low flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 sm:gap-space-8">
                <div class="space-y-space-3 flex-1">
                    <span class="font-label-xs text-label-xs bg-surface-container text-on-surface-variant px-space-2.5 py-1 rounded font-semibold">Dinamički Vidžet Upravljač</span>
                    <h4 class="text-xl sm:text-2xl text-on-surface font-semibold">Reaktivni Analitički Blok</h4>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                        Ovaj modularni vidžet se ubacuje jednim klikom u bilo koju stranicu i automatski prikuplja podatke iz Celios telemetrijskog engine-a.
                    </p>
                    <div class="flex items-center gap-space-3 pt-space-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            <span class="font-label-sm text-label-sm text-on-surface">Laravel Cache</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-tertiary-container"></span>
                            <span class="font-label-sm text-label-sm text-on-surface">CDN Edge</span>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-96 p-4 sm:p-space-4 rounded-xl bg-surface-container-lowest shadow-sm border border-surface-container flex flex-col gap-space-3">
                    <div class="flex items-center justify-between">
                        <span class="font-label-xs text-label-xs text-outline uppercase tracking-wider">Učitavanje po regionima</span>
                        <span class="font-label-xs text-label-xs bg-secondary-fixed text-on-secondary-fixed px-space-2 py-0.5 rounded font-semibold">Live Feed</span>
                    </div>
                    <svg class="w-full h-24 text-primary" fill="none" viewBox="0 0 300 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 80 Q 40 70, 70 40 T 140 50 T 210 20 T 300 30" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="3"></path>
                        <path d="M0 80 Q 40 70, 70 40 T 140 50 T 210 20 T 300 30 L 300 100 L 0 100 Z" fill="currentColor" fill-opacity="0.08"></path>
                        <circle cx="210" cy="20" fill="currentColor" r="4"></circle>
                        <circle cx="140" cy="50" fill="currentColor" fill-opacity="0.6" r="3"></circle>
                        <circle cx="70" cy="40" fill="currentColor" fill-opacity="0.6" r="3"></circle>
                    </svg>
                    <div class="flex items-center justify-between pt-space-2 border-t border-surface-container text-body-sm font-body-sm text-on-surface-variant">
                        <span>Beograd CDN: <strong>14ms</strong></span>
                        <span>Frankfurt Edge: <strong>22ms</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const langData = @json($langDataMap);

    function switchLayoutTab(tabName) {
        const panels = document.querySelectorAll('.layout-panel');
        panels.forEach(panel => panel.classList.add('hidden'));

        const buttons = document.querySelectorAll('.layout-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-surface-container-lowest', 'text-on-surface', 'shadow-sm');
            btn.classList.add('text-on-surface-variant');
        });

        const activePanel = document.getElementById('layout-' + tabName);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        const activeBtn = document.getElementById('btn-tab-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('text-on-surface-variant');
            activeBtn.classList.add('bg-surface-container-lowest', 'text-on-surface', 'shadow-sm');
        }

        const slugEl = document.getElementById('preview-slug');
        if (slugEl) {
            slugEl.innerText = tabName === 'split' ? 'hero-minimal-split' :
                               tabName === 'bento' ? 'bento-portfolio-grid' :
                               tabName === 'case'  ? 'case-study-feed' : 'interactive-widget';
        }
    }

    function switchPreviewLang(lang) {
        const srBtn = document.getElementById('lang-sr-btn');
        const enBtn = document.getElementById('lang-en-btn');

        if (lang === 'sr') {
            if (srBtn) {
                srBtn.className = "px-space-2 py-0.5 rounded-md font-label-xs text-label-xs bg-surface-container-lowest text-on-surface font-semibold shadow-sm";
                enBtn.className = "px-space-2 py-0.5 rounded-md font-label-xs text-label-xs text-on-surface-variant font-semibold hover:text-on-surface";
            }
        } else {
            if (srBtn) {
                srBtn.className = "px-space-2 py-0.5 rounded-md font-label-xs text-label-xs text-on-surface-variant font-semibold hover:text-on-surface";
                enBtn.className = "px-space-2 py-0.5 rounded-md font-label-xs text-label-xs bg-surface-container-lowest text-on-surface font-semibold shadow-sm";
            }
        }

        document.querySelectorAll('[data-lang-text-key]').forEach(el => {
            const key = el.getAttribute('data-lang-text-key');
            if (langData[lang] && langData[lang][key]) {
                el.innerText = langData[lang][key];
            }
        });
    }

    function randomizeThemeVariant() {
        const slug = document.getElementById('preview-slug');
        if (slug) {
            const themes = ['indigo-classic', 'slate-monolith', 'cobalt-sharp', 'minimalist-carbon'];
            const pick = themes[Math.floor(Math.random() * themes.length)];
            slug.innerText = slug.innerText.split('&theme=')[0] + '&theme=' + pick;
        }
    }
</script>

