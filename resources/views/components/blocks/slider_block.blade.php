@php
    $images = $data['images'] ?? [];
    $autoplay = $data['autoplay'] ?? true;
    $locale = app()->getLocale();

    $title = is_array($data['title'] ?? null) 
        ? ($data['title'][$locale] ?? $data['title']['sr'] ?? $data['title']['en'] ?? null) 
        : ($data['title'] ?? null);
    $subtitle = is_array($data['subtitle'] ?? null) 
        ? ($data['subtitle'][$locale] ?? $data['subtitle']['sr'] ?? $data['subtitle']['en'] ?? null) 
        : ($data['subtitle'] ?? null);
    $btnText = is_array($data['button_text'] ?? null) 
        ? ($data['button_text'][$locale] ?? $data['button_text']['sr'] ?? $data['button_text']['en'] ?? null) 
        : ($data['button_text'] ?? null);
    $btnUrl = $data['button_url'] ?? null;
@endphp

@if(!empty($images) && is_array($images))
    <section class="relative py-space-6 sm:py-space-8 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full">
        <div class="swiper main-slider rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg h-[300px] sm:h-[400px] md:h-[500px] lg:h-[550px] relative border border-surface-container-high/60"
             data-autoplay="{{ $autoplay ? 'true' : 'false' }}">

            {{-- Swiper slajdovi (Slike) --}}
            <div class="swiper-wrapper">
                @foreach($images as $imagePath)
                    @if(is_string($imagePath) && !empty($imagePath))
                        @php
                            $imgSrc = str_starts_with($imagePath, 'http') || str_starts_with($imagePath, '/') ? $imagePath : \Illuminate\Support\Facades\Storage::url($imagePath);
                        @endphp
                        <div class="swiper-slide relative bg-surface-container">
                            <img src="{{ $imgSrc }}"
                                 class="w-full h-full object-cover"
                                 alt="{{ $title ?? 'Slider image' }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Sadržaj entiteta (Naslov, podnaslov i dugme) preko slika --}}
            @if($title || $subtitle || ($btnText && $btnUrl))
                <div class="absolute inset-0 z-10 pointer-events-none flex items-end justify-start p-5 sm:p-space-8 md:p-space-12 text-left">
                    <div class="max-w-2xl text-white pointer-events-auto">
                        @if($title)
                            <h2 class="text-2xl sm:text-3xl md:text-5xl font-display-lg font-bold mb-2 sm:mb-3 tracking-tight drop-shadow-md break-words">
                                {{ $title }}
                            </h2>
                        @endif

                        @if($subtitle)
                            <p class="text-sm sm:text-base md:text-xl text-slate-200 mb-4 sm:mb-6 drop-shadow leading-relaxed break-words line-clamp-3 sm:line-clamp-none">
                                {{ $subtitle }}
                            </p>
                        @endif

                        @if($btnText && $btnUrl)
                            <a href="{{ $btnUrl }}"
                               class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-container text-on-primary font-label-md font-semibold px-5 sm:px-6 py-2.5 sm:py-3.5 rounded-xl transition shadow-lg w-full sm:w-auto">
                                <span>{{ $btnText }}</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Swiper navigacija --}}
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next text-white"></div>
            <div class="swiper-button-prev text-white"></div>
        </div>
    </section>
@endif
