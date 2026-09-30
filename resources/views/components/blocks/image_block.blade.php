@php
    $locale = app()->getLocale();
    $imageId = $data['image_id'] ?? null;
    $imageUrl = null;

    if ($imageId) {
        $mediaClass = config('curator.model', \Awcodes\Curator\Models\Media::class);
        $media = is_numeric($imageId) ? $mediaClass::find($imageId) : null;
        $imageUrl = $media?->url ?? (is_string($imageId) ? \Illuminate\Support\Facades\Storage::url($imageId) : null);
    }

    $caption = is_array($data['caption'] ?? null) 
        ? ($data['caption'][$locale] ?? $data['caption']['sr'] ?? $data['caption']['en'] ?? '') 
        : ($data['caption'] ?? '');

    $alt = is_array($data['alt'] ?? null) 
        ? ($data['alt'][$locale] ?? $data['alt']['sr'] ?? $data['alt']['en'] ?? '') 
        : ($data['alt'] ?? 'Image');
@endphp

@if($imageUrl)
<section class="py-space-6 sm:py-space-8 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full">
    <figure class="max-w-4xl mx-auto rounded-2xl sm:rounded-3xl overflow-hidden bg-surface-container-lowest border border-surface-container-high/60 shadow-sm">
        <a href="{{ $imageUrl }}" class="glightbox block overflow-hidden group">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $alt }}" 
                 class="w-full h-auto max-h-[300px] sm:max-h-[450px] md:max-h-[600px] object-cover group-hover:scale-[1.02] transition-transform duration-500 lazyload" 
                 loading="lazy">
        </a>
        @if($caption)
            <figcaption class="p-3 sm:p-space-4 text-center font-body-sm text-body-sm text-on-surface-variant border-t border-surface-container break-words">
                {{ $caption }}
            </figcaption>
        @endif
    </figure>
</section>
@endif
