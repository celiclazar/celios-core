@php
    $locale = app()->getLocale();
    $get = function($field, $default = '') use ($data, $locale) {
        return $data[$field][$locale] 
            ?? $data[$field]['sr'] 
            ?? $data[$field]['en'] 
            ?? $default;
    };

    $pill = $get('status_pill', 'Trenutno Aktivan: Celios Sandbox Environment');
    $heading = $get('heading', 'Isprobajte Celios Admin u testnom režimu');
    $desc = $get('description', 'Istražite upravljanje sadržajem, višejezičnost i modularne blokove u realnom Filament v3 okruženju. Bez instalacije, bez kreditne kartice.');
    $btnText = $get('button_text', 'Uđi u Admin Kontrolnu Tablu (Test Demo)');
    $note1 = $get('footer_note_1', 'Demo nalog unapred popunjen');
    $note2 = $get('footer_note_2', 'Reset baze svakih 60 minuta');
    $adminUrl = $data['admin_url'] ?? url('/admin');
@endphp

<!-- POZIV NA AKCIJU (TEST MODE BANNER) -->
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="test-mode-banner">
    <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-br from-primary via-primary-container to-tertiary text-on-primary p-6 sm:p-space-8 md:p-space-12 shadow-xl relative overflow-hidden">
        <!-- Decorative Background Blobs -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-black/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto text-center flex flex-col items-center gap-space-4 sm:gap-space-6">
            @if($pill)
                <div class="inline-flex items-center gap-space-2 px-space-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-on-primary font-label-xs text-label-xs uppercase tracking-wider font-bold">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>{{ $pill }}</span>
                </div>
            @endif

            <h2 class="text-2xl sm:text-4xl lg:text-5xl font-bold tracking-tight leading-tight break-words">
                {{ $heading }}
            </h2>

            @if($desc)
                <p class="text-base sm:text-lg md:text-xl text-on-primary-container max-w-2xl leading-relaxed">
                    {{ $desc }}
                </p>
            @endif

            <div class="flex flex-col sm:flex-row items-center justify-center gap-space-4 pt-space-2 w-full sm:w-auto">
                <button class="w-full sm:w-auto inline-flex items-center justify-center gap-space-3 px-6 sm:px-space-8 py-3.5 sm:py-4 rounded-xl bg-surface-container-lowest text-on-surface font-label-md text-label-md font-bold hover:bg-surface hover:shadow-2xl transition-all shadow-lg active:scale-95 group text-center" onclick="openDemoModal()">
                    <span class="material-symbols-outlined text-primary text-xl sm:text-2xl group-hover:rotate-12 transition-transform">dashboard_customize</span>
                    <span>{{ $btnText }}</span>
                </button>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-on-primary-container font-label-xs text-label-xs mt-2 text-center">
                @if($note1)
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">lock_open</span> {{ $note1 }}
                    </span>
                @endif
                @if($note1 && $note2)
                    <span class="hidden sm:inline">•</span>
                @endif
                @if($note2)
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-base">refresh</span> {{ $note2 }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</section>
