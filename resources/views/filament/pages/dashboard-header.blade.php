<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <!-- Environment Badge -->
            <div class="inline-flex items-center gap-2 text-xs mb-2">
                <span class="px-2.5 py-0.5 rounded-full font-bold bg-[#d6e3ff] text-[#00458d] tracking-wide">
                    CELIOS CMS
                </span>
                <span class="text-[#424751] font-medium flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#108859]"></span>
                    Sistem aktivan
                </span>
            </div>

            <!-- Greeting Headline -->
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#111c2e]">
                Dobrodošao nazad, {{ auth()->user()->name ?? 'Administrator' }}
            </h1>
            <p class="text-sm sm:text-base text-[#424751] mt-1 font-normal">
                Sve usluge i lokalizacija (SR/EN) funkcionišu stabilno.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="/" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white hover:bg-[#f1f3ff] border border-[#dfe8ff] text-xs sm:text-sm font-semibold text-[#111c2e] shadow-xs transition-colors">
                <svg class="w-4 h-4 text-[#424751]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Pregledaj sajt
            </a>

            <a href="/admin/pages/create" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white hover:bg-[#f1f3ff] border border-[#dfe8ff] text-xs sm:text-sm font-semibold text-[#111c2e] shadow-xs transition-colors">
                <svg class="w-4 h-4 text-[#424751]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                + Nova Stranica
            </a>

            <a href="/admin/posts/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4474bf] hover:bg-[#275ba5] text-xs sm:text-sm font-semibold text-white shadow-sm shadow-[#4474bf]/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Nova Objava
            </a>
        </div>
    </div>
</div>
