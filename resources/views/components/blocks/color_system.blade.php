@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $sectionTag = $get('section_tag', 'Adaptivni Kolor Sistem');
    $heading = $get('heading', 'Fluidni Prelaz Između Svetlog i Tamnog Režima');
    $desc = $get('description', 'Dizajneri nisu primorani na kompromise. Celios tokeni automatski mapiraju kontraste (surface, on-surface, primary i accente) tako da vaš portfolio zadržava identičan nivo elegancije i čitljivosti u bilo koje doba dana.');
    $chip1 = $get('chip_1', 'Automatska detekcija OS postavki');
    $chip2 = $get('chip_2', 'Tailwind Semantic Tokens');
    $footerNote = $get('footer_note', 'Generisano bez hardkodovanih boja • 100% Tailwind Semantic Classes');
@endphp

<!-- ADAPTIVNI KOLOR SISTEM TEASER -->
<section class="py-space-8 pb-space-12 sm:pb-space-16 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full">
    <div class="rounded-2xl sm:rounded-3xl bg-surface-container-lowest p-5 sm:p-space-8 border border-surface-container-high/60 shadow-sm overflow-hidden">
        <div class="flex flex-col lg:flex-row items-center gap-6 sm:gap-space-8">
            <div class="flex-1 space-y-space-4">
                @if($sectionTag)
                    <div class="inline-flex items-center gap-2 px-space-3 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-xs text-label-xs font-semibold">
                        <span class="material-symbols-outlined text-sm">brightness_medium</span>
                        <span>{{ $sectionTag }}</span>
                    </div>
                @endif
                <h3 class="text-xl sm:text-2xl lg:text-3xl text-on-surface font-bold break-words">
                    {{ $heading }}
                </h3>
                @if($desc)
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed text-sm sm:text-base">
                        {{ $desc }}
                    </p>
                @endif
                <div class="flex flex-wrap items-center gap-2 sm:gap-space-4 pt-space-2">
                    @if($chip1)
                        <div class="p-2.5 sm:p-space-3 rounded-lg bg-surface-container flex items-center gap-2 sm:gap-space-3">
                            <span class="material-symbols-outlined text-primary text-lg sm:text-xl">contrast</span>
                            <span class="font-label-sm text-xs sm:text-label-sm text-on-surface font-semibold">{{ $chip1 }}</span>
                        </div>
                    @endif
                    @if($chip2)
                        <div class="p-2.5 sm:p-space-3 rounded-lg bg-surface-container flex items-center gap-2 sm:gap-space-3">
                            <span class="material-symbols-outlined text-tertiary text-lg sm:text-xl">palette</span>
                            <span class="font-label-sm text-xs sm:text-label-sm text-on-surface font-semibold">{{ $chip2 }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mini Visual Simulator -->
            <div class="flex-1 w-full flex flex-col gap-space-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-3">
                    <div class="p-space-4 rounded-xl bg-surface-container-low border border-surface-container-high shadow-sm flex flex-col justify-between h-40 sm:h-44">
                        <div class="flex items-center justify-between">
                            <span class="font-label-xs text-label-xs font-semibold text-on-surface">Light Mode (Default)</span>
                            <span class="material-symbols-outlined text-lg text-on-surface-variant">light_mode</span>
                        </div>
                        <div class="space-y-2">
                            <div class="w-1/2 h-3 rounded bg-surface-container"></div>
                            <div class="w-full h-2 rounded bg-surface-container-highest"></div>
                            <div class="w-3/4 h-2 rounded bg-surface-container-highest"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-label-xs text-label-xs text-outline font-code-sm">Surface #faf8ff</span>
                            <span class="px-space-2 py-0.5 rounded font-label-xs text-label-xs bg-primary text-on-primary">Button</span>
                        </div>
                    </div>
                    <div class="p-space-4 rounded-xl bg-inverse-surface shadow-sm text-inverse-on-surface flex flex-col justify-between h-40 sm:h-44">
                        <div class="flex items-center justify-between">
                            <span class="font-label-xs text-label-xs font-semibold text-inverse-on-surface">Dark Mode Preview</span>
                            <span class="material-symbols-outlined text-lg text-inverse-primary">dark_mode</span>
                        </div>
                        <div class="space-y-2">
                            <div class="w-1/2 h-3 rounded bg-surface-variant/20"></div>
                            <div class="w-full h-2 rounded bg-surface-variant/10"></div>
                            <div class="w-3/4 h-2 rounded bg-surface-variant/10"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-label-xs text-label-xs text-outline-variant font-code-sm">Inverse #283044</span>
                            <span class="px-space-2 py-0.5 rounded font-label-xs text-label-xs bg-inverse-primary text-on-primary-fixed font-semibold">Active</span>
                        </div>
                    </div>
                </div>
                @if($footerNote)
                    <div class="text-center">
                        <span class="font-code-sm text-xs sm:text-code-sm text-on-surface-variant">{{ $footerNote }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
