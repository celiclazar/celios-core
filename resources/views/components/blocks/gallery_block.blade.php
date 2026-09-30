@php
    $images = $data['images'] ?? [];
    $layout = $data['layout'] ?? 'grid';
    $columns = $data['columns'] ?? '3';
    $lightbox = $data['enable_lightbox'] ?? true;

    $mediaClass = config('curator.model', \Awcodes\Curator\Models\Media::class);

    $resolvedImages = [];
    if (!empty($images) && is_array($images)) {
        foreach ($images as $img) {
            if (is_numeric($img)) {
                $m = $mediaClass::find($img);
                if ($m) {
                    $resolvedImages[] = [
                        'url' => $m->url,
                        'alt' => $m->alt ?? $m->title ?? 'Gallery image',
                        'caption' => $m->caption ?? '',
                    ];
                }
            } elseif (is_string($img)) {
                $resolvedImages[] = [
                    'url' => str_starts_with($img, 'http') || str_starts_with($img, '/') ? $img : \Illuminate\Support\Facades\Storage::url($img),
                    'alt' => 'Gallery image',
                    'caption' => '',
                ];
            } elseif (is_array($img) && isset($img['url'])) {
                $resolvedImages[] = $img;
            }
        }
    }

    $gridCols = match((string)$columns) {
        '2' => 'grid-cols-1 sm:grid-cols-2',
        '4' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    };
@endphp

@if(!empty($resolvedImages))
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="gallery">
    @if($layout === 'slider')
        <div class="swiper main-slider rounded-2xl sm:rounded-3xl overflow-hidden shadow-md h-[260px] sm:h-[380px] md:h-[480px] lg:h-[550px] relative" data-autoplay="true">
            <div class="swiper-wrapper">
                @foreach($resolvedImages as $image)
                    <div class="swiper-slide relative bg-surface-container">
                        <img src="{{ $image['url'] }}" class="w-full h-full object-cover" alt="{{ $image['alt'] }}">
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next text-white"></div>
            <div class="swiper-button-prev text-white"></div>
        </div>
    @else
        <div class="grid {{ $gridCols }} gap-3 sm:gap-space-4">
            @foreach($resolvedImages as $image)
                <div class="overflow-hidden rounded-xl sm:rounded-2xl bg-surface-container-lowest border border-surface-container-high/60 shadow-sm hover:shadow-md transition group">
                    @if($lightbox)
                        <a href="{{ $image['url'] }}" class="glightbox block relative overflow-hidden aspect-[4/3]">
                            <img src="{{ $image['url'] }}"
                                 alt="{{ $image['alt'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 lazyload"
                                 loading="lazy">
                        </a>
                    @else
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $image['url'] }}"
                                 alt="{{ $image['alt'] }}"
                                 class="w-full h-full object-cover lazyload"
                                 loading="lazy">
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
@endif
