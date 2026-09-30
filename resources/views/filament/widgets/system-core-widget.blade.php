<div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-[#e8eeff] text-[#4474bf] flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <h2 class="text-base font-bold text-[#111c2e] tracking-tight">
                Sistem & Layout Jezgro
            </h2>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-[#4474bf] ring-4 ring-[#e8eeff]"></span>
    </div>

    <!-- Specifications Table -->
    <div class="divide-y divide-[#f1f3ff] text-xs">
        <div class="py-2.5 flex items-center justify-between">
            <span class="text-[#424751] font-medium">CMS Jezgro</span>
            <span class="font-bold text-[#111c2e]">{{ $cmsVersion }}</span>
        </div>
        <div class="py-2.5 flex items-center justify-between">
            <span class="text-[#424751] font-medium">Admin Platforma</span>
            <span class="font-bold text-[#111c2e]">{{ $adminPlatform }}</span>
        </div>
        <div class="py-2.5 flex items-center justify-between">
            <span class="text-[#424751] font-medium">Backend Okvir</span>
            <span class="font-bold text-[#111c2e]">{{ $backend }}</span>
        </div>
        <div class="py-2.5 flex items-center justify-between">
            <span class="text-[#424751] font-medium">Baza Podataka</span>
            <span class="font-bold text-[#111c2e]">{{ $database }}</span>
        </div>
        <div class="py-2.5 flex items-center justify-between">
            <span class="text-[#424751] font-medium">Aktivna Tema</span>
            <span class="font-bold text-[#111c2e]">{{ $activeTheme }}</span>
        </div>
    </div>

    <!-- Backup Banner -->
    <a href="/admin/backup-manager" class="flex items-center justify-between p-3 bg-[#f1f3ff] hover:bg-[#e8eeff] border border-[#dfe8ff] rounded-xl text-xs transition-colors group">
        <div class="flex items-center gap-2.5">
            <div class="text-[#4474bf] group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span class="font-bold text-[#111c2e] group-hover:text-[#4474bf] transition-colors">Automatski rezervni backup</span>
        </div>
        <div class="flex items-center gap-1.5 text-[#424751] font-medium">
            <span>{{ $backupStatus }}</span>
            <span class="text-xs text-[#4474bf] opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
        </div>
    </a>
</div>
