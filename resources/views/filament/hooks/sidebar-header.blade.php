@php
    $siteName = setting('site_name', config('app.name', 'Production Web'));
    $siteInitial = mb_strtoupper(mb_substr($siteName, 0, 1)) ?: 'W';

    if (app()->isDownForMaintenance()) {
        $envLabel = 'Maintenance';
        $envBadgeBg = 'bg-[#ffdad6] text-[#93000a]';
        $envDotColor = 'bg-[#ba1a1a]';
    } elseif (app()->isProduction()) {
        $envLabel = 'Live';
        $envBadgeBg = 'bg-[#c1ecd5] text-[#005233]';
        $envDotColor = 'bg-[#108859]';
    } elseif (app()->environment('local')) {
        $envLabel = 'Local';
        $envBadgeBg = 'bg-[#dfe8ff] text-[#00458d]';
        $envDotColor = 'bg-[#275ba5]';
    } else {
        $envLabel = ucfirst(app()->environment());
        $envBadgeBg = 'bg-[#bee9d3] text-[#005233]';
        $envDotColor = 'bg-[#3f6654]';
    }
@endphp

<div 
    x-data="{}" 
    :class="$store.sidebar.isOpen ? 'px-4 pt-3 pb-2 space-y-3' : 'px-2 pt-3 pb-2 flex flex-col items-center gap-y-2'"
    class="transition-all duration-300"
>
    <!-- Brand Title & Studio Switcher -->
    <div 
        class="flex items-center group cursor-pointer w-full transition-all duration-200"
        :class="$store.sidebar.isOpen ? 'justify-between' : 'justify-center'"
    >
        <div class="flex items-center gap-2.5">
            <!-- Brand Mark Icon -->
            <div class="w-8 h-8 rounded-lg bg-[#4474bf] flex items-center justify-center text-white font-bold text-xs shadow-sm shadow-[#4474bf]/30 shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 3a9 9 0 0 1 9 9"></path>
                </svg>
            </div>

            <!-- Brand Name & Studio Label (Hidden when sidebar is collapsed) -->
            <div x-show="$store.sidebar.isOpen" x-cloak class="truncate">
                <div class="text-sm font-bold text-[#111c2e] leading-tight tracking-tight">Celios</div>
                <div class="text-[10px] font-semibold text-[#424751] tracking-wider uppercase">CMS STUDIO</div>
            </div>
        </div>

        <!-- Chevron (Hidden when sidebar is collapsed) -->
        <div x-show="$store.sidebar.isOpen" x-cloak class="text-[#737782] group-hover:text-[#111c2e] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
            </svg>
        </div>
    </div>

    <!-- Dynamic Environment Pill (Hidden when sidebar is collapsed) -->
    <a 
        href="{{ url('/') }}"
        target="_blank"
        title="Otvori sajt ({{ $siteName }})"
        x-show="$store.sidebar.isOpen" 
        x-cloak 
        class="w-full flex items-center justify-between px-3 py-2 bg-[#f1f3ff] hover:bg-[#e8eeff] border border-[#dfe8ff] rounded-xl transition-colors group/pill"
    >
        <div class="flex items-center gap-2 truncate">
            <div class="w-5 h-5 rounded-md bg-[#4474bf] text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                {{ $siteInitial }}
            </div>
            <span class="text-xs font-semibold text-[#111c2e] truncate group-hover/pill:text-[#4474bf] transition-colors">
                {{ $siteName }}
            </span>
        </div>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $envBadgeBg }} shrink-0">
            <span class="w-1.5 h-1.5 rounded-full {{ $envDotColor }} animate-pulse"></span>
            {{ $envLabel }}
        </span>
    </a>
</div>
