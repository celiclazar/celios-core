@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $photoUrl = $data['photo_url'] ?? 'https://lh3.googleusercontent.com/aida/AEtjO1UU1cY31xFr14rSP0jI718ew9dhGfi6yJolDgQ_8AIxsjp2a3xkkN6yOKqbhVCLm0dflK0rbwxIw3y1Z866K6Nnu8E0eUQUC7wTrParVz4K6uNsjTNloRRLDvMJaFbG9Wtz3v1K-6UsZOdqx9YI109M5KZNCOMbqwQvnTCvlLhCE8WY-IO3670lKXgfkus3QJFiycEkhWCroXmDOw65fEE9MReqRXyrq_fuVbY9ijLVZRASocFv2L1cxg0';
    $authorName = $data['author_name'] ?? 'Filip V.';
    $authorRole = $data['author_role'] ?? 'Full-stack Developer & UI Arhitekta';
    $authorLocation = $data['author_location'] ?? 'Beograd, Srbija • Remote & Consulting';
    $authorEmail = $data['author_email'] ?? 'filip@celios.io';

    $github = $data['github_url'] ?? '#';
    $linkedin = $data['linkedin_url'] ?? '#';
    $twitter = $data['twitter_url'] ?? '#';

    $sectionTag = $get('section_tag', 'O Autoru & Motivacija');
    $sectionHeading = $get('section_heading', 'Zašto sam napravio Celios CMS?');
    $story1 = $get('story_paragraph_1', 'Kao full-stack inženjer i dizajner sa dugogodišnjim radom na custom web projektima, konstantno sam se susretao sa istom dilemom: WordPress je bio pretrpan nesigurnim dodacima i teškim za održavanje, dok su pure headless rešenja klijentima delovala previše apstraktno i hladno.');
    $story2 = $get('story_paragraph_2', 'Celios CMS je rođen iz potrebe za čistom, beskompromisnom platformom. Spojio sam eleganciju i reaktivnost Filament v3 administracije sa robusnim Laravel backendom i čistim Tailwind komponentama. Rezultat je CMS u kome klijenti intuitivno uređuju višejezične sadržaje i slažu vizuelne blokove, a developeri imaju 100% čist Blade i PHP kod bez crnih kutija.');

    $rawStack = $data['tech_stack'] ?? 'Laravel 11, Filament v3, Tailwind CSS, Alpine.js, Livewire 3, PostgreSQL / Redis';
    $stack = is_string($rawStack) ? array_filter(array_map('trim', explode(',', $rawStack))) : (is_array($rawStack) ? $rawStack : []);

    $style = $data['style'] ?? [];
    $widthClass = $style['width'] ?? 'max-w-6xl';
    $minHeight = ($style['min_height'] ?? 'auto') === 'auto' ? '' : ($style['min_height'] ?? '');
    $paddingY = $style['padding_y'] ?? 'py-space-12';
    $rounded = $style['rounded'] ?? 'rounded-3xl';
    $cardStyle = ($style['card_style'] ?? 'card') === 'flat' ? 'bg-transparent border-0 shadow-none' : 'bg-surface-container-lowest border border-surface-container-high/70 shadow-sm';
@endphp

<!-- "KO SAM JA" (O AUTORU / DEVELOPERU I DIZAJNERU) -->
<section class="mx-auto px-gutter-md sm:px-gutter-lg w-full {{ $widthClass }} {{ $minHeight }} {{ $paddingY }}" id="o-autoru">
    <div class="{{ $rounded }} {{ $cardStyle }} p-5 sm:p-space-8 lg:p-space-10 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-space-10">
            <!-- Author Image Column -->
            <div class="w-full lg:w-1/3 flex flex-col items-center text-center">
                <div class="relative group">
                    <div class="w-44 h-44 sm:w-52 sm:h-52 md:w-60 md:h-60 rounded-3xl overflow-hidden shadow-lg border-4 border-surface-container-low bg-surface-container relative z-10">
                        <img alt="{{ $authorName }} — {{ $authorRole }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $photoUrl }}"/>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-primary text-on-primary flex items-center justify-center shadow-md z-20">
                        <span class="material-symbols-outlined text-lg sm:text-xl">verified</span>
                    </div>
                    <div class="absolute inset-0 bg-primary/10 rounded-3xl blur-xl -z-0"></div>
                </div>
                <div class="mt-space-4">
                    <h3 class="text-xl sm:text-2xl font-bold text-on-surface">{{ $authorName }}</h3>
                    <p class="font-label-md text-label-md text-primary font-semibold mt-0.5">{{ $authorRole }}</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $authorLocation }}</p>
                </div>

                <!-- Social Links / Badges -->
                <div class="flex flex-wrap justify-center items-center gap-2 mt-space-4">
                    <a class="p-2 rounded-xl bg-surface-container-low hover:bg-surface-container hover:text-primary text-on-surface-variant transition-colors" href="{{ $github }}" title="GitHub">
                        <span class="material-symbols-outlined text-xl">code</span>
                    </a>
                    <a class="p-2 rounded-xl bg-surface-container-low hover:bg-surface-container hover:text-primary text-on-surface-variant transition-colors" href="{{ $linkedin }}" title="LinkedIn / Kontakt">
                        <span class="material-symbols-outlined text-xl">person</span>
                    </a>
                    <a class="p-2 rounded-xl bg-surface-container-low hover:bg-surface-container hover:text-primary text-on-surface-variant transition-colors" href="{{ $twitter }}" title="Twitter / X">
                        <span class="material-symbols-outlined text-xl">alternate_email</span>
                    </a>
                    <a class="p-2 rounded-xl bg-surface-container-low hover:bg-surface-container hover:text-primary text-on-surface-variant transition-colors" href="mailto:{{ $authorEmail }}" title="Direktan Email">
                        <span class="material-symbols-outlined text-xl">mail</span>
                    </a>
                </div>
            </div>

            <!-- Bio & Story Column -->
            <div class="w-full lg:w-2/3 flex flex-col justify-between">
                <div>
                    @if($sectionTag)
                        <div class="inline-flex items-center gap-2 px-space-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-xs text-label-xs font-semibold mb-space-3">
                            <span class="material-symbols-outlined text-sm">terminal</span>
                            <span>{{ $sectionTag }}</span>
                        </div>
                    @endif
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-on-surface tracking-tight break-words">
                        {{ $sectionHeading }}
                    </h2>
                    <div class="space-y-space-3 font-body-md text-body-md text-on-surface-variant mt-space-4 leading-relaxed text-sm sm:text-base">
                        @if($story1)
                            <p>{{ $story1 }}</p>
                        @endif
                        @if($story2)
                            <p>{!! nl2br(e($story2)) !!}</p>
                        @endif
                    </div>
                </div>

                <!-- Tech Stack Badges -->
                @if(!empty($stack))
                    <div class="mt-space-6 pt-space-5 border-t border-surface-container">
                        <span class="block font-label-xs text-label-xs text-outline uppercase tracking-wider font-semibold mb-space-2">
                            {{ $locale === 'sr' ? 'Primarni Tehnološki Stack' : 'Primary Technology Stack' }}
                        </span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($stack as $tech)
                                <span class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg bg-surface-container-low text-on-surface font-code-sm text-xs sm:text-code-sm font-semibold border border-surface-container">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@php
    $sameAsLinks = array_values(array_filter([
        !empty($github) && $github !== '#' ? $github : null,
        !empty($linkedin) && $linkedin !== '#' ? $linkedin : null,
        !empty($twitter) && $twitter !== '#' ? $twitter : null,
    ]));

    $schemaAuthor = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $authorName,
        'jobTitle' => $authorRole,
        'image' => $photoUrl,
    ];

    if (!empty($authorEmail)) {
        $schemaAuthor['email'] = $authorEmail;
    }

    if (!empty($authorLocation)) {
        $schemaAuthor['workLocation'] = [
            '@type' => 'Place',
            'name' => $authorLocation,
        ];
    }

    if (!empty($sameAsLinks)) {
        $schemaAuthor['sameAs'] = $sameAsLinks;
    }
@endphp

@if(!empty($authorName))
<script type="application/ld+json">
{!! json_encode($schemaAuthor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
