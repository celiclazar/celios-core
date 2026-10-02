<div class="bg-white border border-[#dfe8ff] rounded-2xl p-5 shadow-xs space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-[#111c2e] tracking-tight">
            Brzi log promena
        </h2>
        <a href="/admin/activities" class="text-xs font-semibold text-[#4474bf] hover:text-[#275ba5] hover:underline transition-colors">
            Vidi sve &rarr;
        </a>
    </div>

    <!-- Timeline List -->
    @if(empty($activities))
        <div class="py-6 text-center text-xs text-[#737782] flex flex-col items-center justify-center gap-2">
            <svg class="w-8 h-8 text-[#dfe8ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Nema zabeleženih aktivnosti u sistemu.</span>
        </div>
    @else
        <div class="relative pl-5 space-y-4 before:content-[''] before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#dfe8ff]">
            @foreach($activities as $act)
                <a href="{{ $act['url'] ?? '/admin/activities' }}" class="relative block group">
                    <!-- Timeline Dot -->
                    <div class="absolute -left-5 top-1.5 w-3 h-3 rounded-full border-2 border-white {{ $act['dot'] }} ring-2 ring-[#e8eeff] group-hover:scale-125 transition-transform"></div>
                    <div>
                        <div class="text-xs font-bold text-[#111c2e] group-hover:text-[#4474bf] transition-colors leading-snug">
                            {{ $act['title'] }}
                        </div>
                        <div class="text-[11px] text-[#424751] mt-0.5 font-medium">
                            {{ $act['subtitle'] }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
