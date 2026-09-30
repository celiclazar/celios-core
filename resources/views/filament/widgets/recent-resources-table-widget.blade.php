<div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-[#111c2e] tracking-tight">
                    Nedavno ažurirani resursi
                </h2>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-[#f1f3ff] text-[#424751]">
                    Filtrirano: 
                    @if($currentFilter === 'pages')
                        Stranice
                    @elseif($currentFilter === 'posts')
                        Blog
                    @else
                        Sve
                    @endif
                </span>
            </div>
            <p class="text-xs text-[#424751] mt-0.5">
                Pregled promena na dinamičkim strukturama, stranicama i blog unosima. Kliknite na red za izmenu.
            </p>
        </div>

        <!-- Search and Filter -->
        <div class="flex items-center gap-2">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Filtriraj po naslovu..." 
                    class="w-48 sm:w-56 text-xs bg-[#f1f3ff] border border-[#dfe8ff] rounded-lg pl-8 pr-3 py-1.5 text-[#111c2e] placeholder-[#737782] focus:outline-none focus:border-[#4474bf] focus:ring-1 focus:ring-[#4474bf]"
                />
                <svg class="w-3.5 h-3.5 text-[#737782] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Filter Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button 
                    type="button" 
                    @click="open = !open"
                    title="Filtriraj tip resursa"
                    class="p-2 rounded-lg bg-[#f1f3ff] border border-[#dfe8ff] text-[#424751] hover:text-[#111c2e] hover:bg-[#e8eeff] transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>
                <div 
                    x-show="open" 
                    @click.outside="open = false" 
                    x-transition 
                    style="display: none;"
                    class="absolute right-0 mt-2 w-36 bg-white border border-[#dfe8ff] rounded-xl shadow-lg z-20 py-1 text-xs"
                >
                    <button 
                        type="button" 
                        wire:click="setFilter('all'); open = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-[#f1f3ff] flex items-center justify-between {{ $currentFilter === 'all' ? 'font-bold text-[#4474bf]' : 'text-[#111c2e]' }}"
                    >
                        Sve @if($currentFilter === 'all') <span>✓</span> @endif
                    </button>
                    <button 
                        type="button" 
                        wire:click="setFilter('pages'); open = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-[#f1f3ff] flex items-center justify-between {{ $currentFilter === 'pages' ? 'font-bold text-[#4474bf]' : 'text-[#111c2e]' }}"
                    >
                        Stranice @if($currentFilter === 'pages') <span>✓</span> @endif
                    </button>
                    <button 
                        type="button" 
                        wire:click="setFilter('posts'); open = false" 
                        class="w-full text-left px-3 py-1.5 hover:bg-[#f1f3ff] flex items-center justify-between {{ $currentFilter === 'posts' ? 'font-bold text-[#4474bf]' : 'text-[#111c2e]' }}"
                    >
                        Blog @if($currentFilter === 'posts') <span>✓</span> @endif
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[11px] font-bold text-[#424751] uppercase tracking-wider border-b border-[#f1f3ff] bg-[#f9f9ff]/50">
                    <th class="py-3 px-3">Naslov / Stavka</th>
                    <th class="py-3 px-3">Tip Modula</th>
                    <th class="py-3 px-3 text-center">Jezici</th>
                    <th class="py-3 px-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1f3ff]">
                @forelse($resources as $res)
                    <tr 
                        onclick="window.location.href='{{ $res['edit_url'] }}'"
                        class="hover:bg-[#f9f9ff] transition-colors group cursor-pointer"
                    >
                        <!-- Title & Subtitle with Icon -->
                        <td class="py-3.5 px-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $res['icon_bg'] }} flex items-center justify-center shrink-0">
                                    @if($res['module'] === 'Portfolio')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    @elseif($res['module'] === 'Stranica')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @elseif($res['module'] === 'Blog')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a 
                                            href="{{ $res['edit_url'] }}" 
                                            onclick="event.stopPropagation();"
                                            class="font-bold text-[#111c2e] group-hover:text-[#4474bf] transition-colors hover:underline truncate"
                                        >
                                            {{ $res['title'] }}
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-[#424751] font-mono truncate">
                                            {{ $res['url'] }}
                                        </span>
                                        @if(!empty($res['public_url']))
                                            <a 
                                                href="{{ $res['public_url'] }}" 
                                                target="_blank" 
                                                onclick="event.stopPropagation();"
                                                title="Otvori na sajtu"
                                                class="text-[#737782] hover:text-[#4474bf] opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Module Pill -->
                        <td class="py-3.5 px-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-[#f1f3ff] text-[#111c2e] border border-[#dfe8ff]">
                                @if($res['module'] === 'Portfolio')
                                    <svg class="w-3 h-3 text-[#4474bf]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @elseif($res['module'] === 'Stranica')
                                    <svg class="w-3 h-3 text-[#4474bf]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($res['module'] === 'Blog')
                                    <svg class="w-3 h-3 text-[#088557]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                @else
                                    <svg class="w-3 h-3 text-[#4474bf]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                @endif
                                {{ $res['module'] }}
                            </span>
                        </td>

                        <!-- Language Badges -->
                        <td class="py-3.5 px-3">
                            <div class="flex items-center justify-center gap-1.5">
                                @foreach($res['languages'] as $lang)
                                    @if($lang['status'] === 'full')
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#c1ecd5] text-[#005233]">
                                            {{ $lang['code'] }}
                                        </span>
                                    @elseif($lang['status'] === 'partial')
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#dfe8ff] text-[#00458d]">
                                            {{ $lang['code'] }}
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#ffdad6] text-[#93000a]">
                                            {{ $lang['code'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>

                        <!-- Status Pill & Actions -->
                        <td class="py-3.5 px-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($res['status_type'] === 'published')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#c1ecd5] text-[#005233]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#108859]"></span>
                                        {{ $res['status'] }}
                                    </span>
                                @elseif($res['status_type'] === 'review')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#dfe8ff] text-[#00458d]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#275ba5]"></span>
                                        {{ $res['status'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#f1f3ff] text-[#424751]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#737782]"></span>
                                        {{ $res['status'] }}
                                    </span>
                                @endif

                                <a 
                                    href="{{ $res['edit_url'] }}" 
                                    onclick="event.stopPropagation();"
                                    title="Izmeni resurs"
                                    class="p-1 rounded-md text-[#737782] hover:text-[#4474bf] hover:bg-[#e8eeff] opacity-0 group-hover:opacity-100 transition-all shrink-0"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-xs text-[#737782]">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 text-[#dfe8ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Nema pronađenih resursa za date parametre pretrage.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 pt-3 border-t border-[#f1f3ff] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#424751]">
        <div>
            Prikazano {{ count($resources) }} od {{ $totalCount }} resursa
        </div>
        <div class="flex items-center gap-1.5">
            <button 
                type="button" 
                wire:click="previousPage"
                @if($currentPage <= 1) disabled @endif
                class="px-2.5 py-1 rounded-md border border-[#dfe8ff] text-[#424751] hover:bg-[#f1f3ff] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            >
                Prethodna
            </button>

            @for($p = 1; $p <= $totalPages; $p++)
                @if($p == 1 || $p == $totalPages || abs($p - $currentPage) <= 1)
                    <button 
                        type="button" 
                        wire:click="gotoPage({{ $p }})"
                        class="w-7 h-7 rounded-md {{ $currentPage == $p ? 'bg-[#4474bf] text-white font-bold' : 'border border-[#dfe8ff] text-[#424751] hover:bg-[#f1f3ff] font-medium' }} flex items-center justify-center transition-colors"
                    >
                        {{ $p }}
                    </button>
                @elseif(abs($p - $currentPage) == 2)
                    <span class="px-1 text-[#737782]">...</span>
                @endif
            @endfor

            <button 
                type="button" 
                wire:click="nextPage({{ $totalPages }})"
                @if($currentPage >= $totalPages) disabled @endif
                class="px-2.5 py-1 rounded-md border border-[#dfe8ff] text-[#424751] hover:bg-[#f1f3ff] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            >
                Sledeća
            </button>
        </div>
    </div>
</div>
