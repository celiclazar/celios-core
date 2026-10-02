<div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="text-[#4474bf] text-lg">✦</span>
            <h2 class="text-base font-bold text-[#111c2e] tracking-tight">
                Brzi vizuelni inventar portfolija
            </h2>
        </div>
        <a href="/admin/media" class="inline-flex items-center gap-1 text-xs font-semibold text-[#4474bf] hover:text-[#275ba5] transition-colors">
            Otvori biblioteku
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>

    <!-- Cards Grid -->
    @if(empty($items))
        <div class="py-8 text-center text-xs text-[#737782] flex flex-col items-center justify-center gap-2">
            <svg class="w-8 h-8 text-[#dfe8ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Nema dodatih medijskih stavki ili istaknutih slika.</span>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            @foreach($items as $item)
                <a href="{{ $item['url'] }}" class="group relative block aspect-[16/11] rounded-xl overflow-hidden shadow-xs border border-[#dfe8ff]/60">
                    <img 
                        src="{{ $item['image'] }}" 
                        alt="{{ $item['title'] }}" 
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-transparent flex flex-col justify-end p-3.5">
                        <span class="text-xs font-bold text-white tracking-wide uppercase group-hover:text-blue-200 transition-colors">
                            {{ $item['title'] }}
                        </span>
                        <span class="text-[11px] font-medium text-white/80 mt-0.5">
                            {{ $item['subtitle'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
