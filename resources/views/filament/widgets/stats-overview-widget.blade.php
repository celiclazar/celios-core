<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Ukupno objava & stranica -->
    <div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#424751] uppercase tracking-wider">
                    Ukupno objava & stranica
                </span>
                <div class="w-9 h-9 rounded-xl bg-[#e8eeff] flex items-center justify-center text-[#4474bf]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2.5 mt-2">
                <span class="text-3xl font-bold text-[#111c2e] tracking-tight">{{ $totalContent }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#c1ecd5] text-[#005233]">
                    +12%
                </span>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#f1f3ff] flex items-center justify-between">
            <div class="w-24 h-6">
                <svg viewBox="0 0 100 28" class="w-full h-full stroke-[#4474bf] fill-none" stroke-width="2.5" stroke-linecap="round">
                    <path d="M 2 24 Q 25 22 45 14 T 98 4" />
                </svg>
            </div>
            <span class="text-xs font-medium text-[#424751]">
                {{ $postsCount }} objave / {{ $pagesCount }} str.
            </span>
        </div>
    </div>

    <!-- Card 2: Aktivni jezici -->
    <div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#424751] uppercase tracking-wider">
                    Aktivni jezici
                </span>
                <div class="w-9 h-9 rounded-xl bg-[#e8eeff] flex items-center justify-center text-[#4474bf]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2.5 mt-2">
                <span class="text-3xl font-bold text-[#111c2e] tracking-tight">2</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#bee9d3] text-[#005233]">
                    SR + EN
                </span>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#f1f3ff] flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5 font-medium text-[#111c2e]">
                <span class="w-2 h-2 rounded-full bg-[#4474bf]"></span>
                98% usklađeno
            </div>
            <span class="text-[#424751]">
                {{ $pendingTranslations }} prevod na čekanju
            </span>
        </div>
    </div>

    <!-- Card 3: Medijska biblioteka -->
    <a href="/admin/media" class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs flex flex-col justify-between hover:border-[#4474bf]/40 transition-colors group">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#424751] uppercase tracking-wider">
                    Medijska biblioteka
                </span>
                <div class="w-9 h-9 rounded-xl bg-[#e8eeff] flex items-center justify-center text-[#4474bf] group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2.5 mt-2">
                <span class="text-3xl font-bold text-[#111c2e] tracking-tight">{{ $mediaCount }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#e8eeff] text-[#4474bf]">
                    Fajlova
                </span>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#f1f3ff] flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5 font-medium text-[#111c2e]">
                <span class="w-2 h-2 rounded-full bg-[#4474bf]"></span>
                Optimizovani resursi
            </div>
            <span class="text-[#4474bf] font-medium group-hover:underline">
                Pregledaj &rarr;
            </span>
        </div>
    </a>

    <!-- Card 4: Prijave i upiti -->
    <a href="/admin/form-submissions" class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs flex flex-col justify-between hover:border-[#4474bf]/40 transition-colors group">
        <div>
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-[#424751] uppercase tracking-wider">
                    Prijave i upiti
                </span>
                <div class="w-9 h-9 rounded-xl bg-[#e8eeff] flex items-center justify-center text-[#4474bf] group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2.5 mt-2">
                <span class="text-3xl font-bold text-[#111c2e] tracking-tight">{{ $submissionsCount }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#c1ecd5] text-[#005233]">
                    Aktivno
                </span>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#f1f3ff] flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5 font-medium text-[#111c2e]">
                <span class="w-2 h-2 rounded-full bg-[#108859]"></span>
                {{ $usersCount }} {{ $usersCount === 1 ? 'korisnik' : 'korisnika' }}
            </div>
            <span class="text-[#4474bf] font-medium group-hover:underline">
                Otvori &rarr;
            </span>
        </div>
    </a>
</div>
