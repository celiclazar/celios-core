<div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-[#e8eeff] text-[#4474bf] flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-base font-bold text-[#111c2e] tracking-tight">
                Lokalizacija (i18n)
            </h2>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#e8eeff] text-[#4474bf]">
            Automated
        </span>
    </div>

    <p class="text-xs text-[#424751] leading-relaxed">
        Sadržaj se dinamički preslikava između primarnog srpskog jezika i globalnog engleskog izdanja.
    </p>

    <!-- Progress Bars -->
    <div class="space-y-3.5 pt-1">
        <!-- Serbian -->
        <div>
            <div class="flex items-center justify-between text-xs font-semibold text-[#111c2e] mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#4474bf]"></span>
                    Srpski (Glavni izvor)
                </div>
                <span class="font-bold text-[#4474bf]">100%</span>
            </div>
            <div class="w-full bg-[#f1f3ff] rounded-full h-2 overflow-hidden">
                <div class="bg-[#4474bf] h-2 rounded-full" style="width: 100%"></div>
            </div>
            <div class="text-[11px] text-[#424751] mt-1">
                {{ $serbianTranslated }}/{{ $totalResources }} resursa prevedeno i odobreno
            </div>
        </div>

        <!-- English -->
        <div>
            <div class="flex items-center justify-between text-xs font-semibold text-[#111c2e] mb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#3f6654]"></span>
                    Engleski (Međunarodno)
                </div>
                <span class="font-bold text-[#3f6654]">{{ $englishPercentage }}%</span>
            </div>
            <div class="w-full bg-[#f1f3ff] rounded-full h-2 overflow-hidden">
                <div class="bg-[#3f6654] h-2 rounded-full" style="width: {{ $englishPercentage }}%"></div>
            </div>
            <div class="text-[11px] text-[#424751] mt-1">
                {{ $englishTranslated }}/{{ $totalResources }} resursa sinhronizovano
            </div>
        </div>
    </div>

    <!-- Info Notice Box -->
    <div class="flex items-start gap-2.5 p-3 bg-[#f1f3ff] border border-[#dfe8ff] rounded-xl text-xs">
        <div class="text-[#4474bf] shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" stroke-width="2"/>
                <path stroke-linecap="round" stroke-width="2" d="M12 16v-4m0-4h.01"/>
            </svg>
        </div>
        <div>
            <div class="font-bold text-[#111c2e]">1 unos čeka prevod</div>
            <div class="text-[11px] text-[#424751] mt-0.5">
                {{ $pendingItem }}
            </div>
        </div>
    </div>

</div>
