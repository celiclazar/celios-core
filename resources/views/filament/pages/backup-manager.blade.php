<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    {{-- Diagnostic & Backup Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Backups --}}
        <div class="bg-white border border-[#dfe8ff] rounded-2xl p-4 shadow-xs">
            <div class="text-xs text-[#424751] font-medium">Ukupno Rezervnih Kopija</div>
            <div class="text-2xl font-bold text-[#111c2e] mt-1 flex items-baseline gap-1.5">
                <span>{{ $stats['total_count'] }}</span>
                <span class="text-xs font-normal text-[#424751]">arhiviranih fajlova</span>
            </div>
            <div class="text-[11px] text-[#424751] mt-1 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full {{ $stats['total_count'] > 0 ? 'bg-[#108859]' : 'bg-[#737782]' }}"></span>
                {{ $stats['total_count'] > 0 ? 'Sistem zaštićen kopijama' : 'Preporučuje se kreiranje kopije' }}
            </div>
        </div>

        {{-- Latest Backup --}}
        <div class="bg-white border border-[#dfe8ff] rounded-2xl p-4 shadow-xs">
            <div class="text-xs text-[#424751] font-medium">Poslednja Kreirana Kopija</div>
            <div class="text-lg font-bold text-[#4474bf] mt-1 truncate">
                {{ $stats['latest'] }}
            </div>
            <div class="text-[11px] text-[#424751] mt-1">
                Automatski i ručni intervali
            </div>
        </div>

        {{-- Total Storage Usage --}}
        <div class="bg-white border border-[#dfe8ff] rounded-2xl p-4 shadow-xs">
            <div class="text-xs text-[#424751] font-medium">Zauzeće Skladišta</div>
            <div class="text-2xl font-bold text-[#111c2e] mt-1">
                {{ $stats['total_size'] }}
            </div>
            <div class="text-[11px] text-[#424751] mt-1">
                Kompresovane ZIP arhive
            </div>
        </div>

        {{-- Storage Destination --}}
        <div class="bg-white border border-[#dfe8ff] rounded-2xl p-4 shadow-xs">
            <div class="text-xs text-[#424751] font-medium">Ciljni Disk (Storage)</div>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-[#dfe8ff] text-[#00458d]">
                    {{ $stats['disk'] }}
                </span>
                <span class="text-xs text-[#108859] font-medium">● Aktivan</span>
            </div>
            <div class="text-[11px] text-[#424751] mt-1">
                Lokalno bezbedno skladište
            </div>
        </div>
    </div>

    {{-- Main Backup Table --}}
    {{ $this->table }}
</x-filament-panels::page>
