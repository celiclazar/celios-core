@php
    $locale = app()->getLocale();
    $heading = $data['heading'][$locale] ?? ($data['heading'][config('locales.default', 'sr')] ?? (is_string($data['heading'] ?? null) ? $data['heading'] : null));
    $subtitle = $data['subtitle'][$locale] ?? ($data['subtitle'][config('locales.default', 'sr')] ?? (is_string($data['subtitle'] ?? null) ? $data['subtitle'] : null));
    $categoryId = $data['category_id'] ?? null;
    $limit = (int) ($data['limit'] ?? 3);
    $layout = $data['layout'] ?? 'grid_3';
    $showExcerpt = $data['show_excerpt'] ?? true;
    $showDate = $data['show_date'] ?? true;
    $showAuthor = $data['show_author'] ?? true;

    $posts = \App\Models\Post::query()
        ->published()
        ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
        ->with(['category', 'author', 'featuredImage'])
        ->orderByRaw('COALESCE(published_at, created_at) DESC')
        ->limit($limit)
        ->get();
@endphp

@if($posts->isNotEmpty())
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="blog-posts">
    @if($heading || $subtitle)
        <div class="text-center max-w-3xl mx-auto mb-space-8 sm:mb-space-12">
            @if($heading)
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-display-lg font-bold text-on-surface tracking-tight break-words">
                    {{ $heading }}
                </h2>
            @endif
            @if($subtitle)
                <p class="font-body-md text-body-md text-on-surface-variant mt-3 leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif

    <div class="@if($layout === 'grid_2') grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-space-6 @elseif($layout === 'list') flex flex-col space-y-4 @else grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-space-6 @endif">
        @foreach($posts as $post)
            @php
                $postTitle = $post->getTranslation('title', $locale, true);
                $postExcerpt = $post->getTranslation('excerpt', $locale, true);
                $postUrl = $post->getUrl($locale);
                $categoryTitle = $post->category?->getTranslation('title', $locale, true);
                $imageUrl = $post->featuredImage?->url ?? null;
            @endphp
            <article class="flex flex-col bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-surface-container-high/60 group">
                @if($imageUrl)
                    <a href="{{ $postUrl }}" class="block aspect-video overflow-hidden bg-surface-container">
                        <img src="{{ $imageUrl }}" alt="{{ $postTitle }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                    </a>
                @else
                    <a href="{{ $postUrl }}" class="block aspect-video bg-gradient-to-tr from-primary/10 to-secondary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-4xl text-primary/40">article</span>
                    </a>
                @endif

                <div class="flex-1 p-5 sm:p-space-6 flex flex-col justify-between">
                    <div>
                        @if($categoryTitle)
                            <div class="mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full font-label-xs text-label-xs font-semibold bg-surface-container text-primary">
                                    {{ $categoryTitle }}
                                </span>
                            </div>
                        @endif

                        <h3 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface line-clamp-2 group-hover:text-primary transition-colors break-words">
                            <a href="{{ $postUrl }}">
                                {{ $postTitle }}
                            </a>
                        </h3>

                        @if($showExcerpt && filled($postExcerpt))
                            <p class="mt-3 font-body-sm text-body-sm text-on-surface-variant line-clamp-3 leading-relaxed break-words">
                                {{ $postExcerpt }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-surface-container flex flex-wrap items-center justify-between gap-2 font-label-xs text-label-xs text-on-surface-variant">
                        <div class="flex items-center space-x-2">
                            @if($showAuthor && $post->author)
                                <span class="font-medium text-on-surface">{{ $post->author->name }}</span>
                            @endif
                            @if($showAuthor && $showDate && $post->author && $post->published_at)
                                <span>•</span>
                            @endif
                            @if($showDate && $post->published_at)
                                <time datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->translatedFormat('d. M Y.') }}
                                </time>
                            @endif
                        </div>

                        <a href="{{ $postUrl }}" class="font-semibold text-primary hover:text-primary-dim inline-flex items-center gap-1 shrink-0">
                            <span>{{ __('blog.read_more') ?? 'Čitaj dalje' }}</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

@php
    $schemaPosts = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => $posts->values()->map(function ($post, $index) use ($locale) {
            $postTitle = $post->getTranslation('title', $locale, true) ?: $post->getTranslation('title', config('locales.default', 'sr'), true);
            $postUrl = $post->getUrl($locale);
            $imageUrl = $post->featuredImage?->url ?? null;
            $excerpt = $post->getTranslation('excerpt', $locale, true);

            $item = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $postTitle,
                'url' => $postUrl,
            ];

            if (!empty($excerpt)) {
                $item['description'] = trim(strip_tags((string) $excerpt));
            }

            if (!empty($imageUrl)) {
                $item['image'] = $imageUrl;
            }

            return $item;
        })->all(),
    ];
@endphp

@if(!empty($schemaPosts['itemListElement']))
<script type="application/ld+json">
{!! json_encode($schemaPosts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endif
