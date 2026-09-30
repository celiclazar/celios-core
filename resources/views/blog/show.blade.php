<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $postTitle = $post->getTranslation('title', $locale, true);
        $postExcerpt = $post->getTranslation('excerpt', $locale, true);
        $postBody = $post->getTranslation('body', $locale, true);
        $categoryTitle = $post->category?->getTranslation('title', $locale, true);
        $imageUrl = $post->featuredImage?->url ?? null;
    @endphp

    <title>{{ $postTitle }} - {{ __('blog.blog') }}</title>
    @if(filled($postExcerpt))
        <meta name="description" content="{{ $postExcerpt }}">
    @endif

    <link rel="canonical" href="{{ $post->getUrl($locale) }}" />

    @foreach(['sr', 'en', 'it'] as $lang)
        @php $langSlug = $post->getTranslation('slug', $lang); @endphp
        @if(!empty($langSlug))
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ url("/{$lang}/blog/{$langSlug}") }}" />
        @endif
    @endforeach

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen flex flex-col selection:bg-primary-fixed selection:text-on-primary-fixed">

    @include('partials.nav', ['currentPost' => $post])

    <main class="flex-grow py-space-8 sm:py-space-12">
        <article class="max-w-4xl mx-auto px-gutter-md sm:px-gutter-lg">

            {{-- Breadcrumb / Category --}}
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-on-surface-variant mb-4 sm:mb-6 font-label-sm">
                <a href="{{ url("/{$locale}/blog") }}" class="hover:text-primary transition-colors font-medium">{{ __('blog.blog') }}</a>
                <span>&rsaquo;</span>
                @if($post->category)
                    <a href="{{ $post->category->getUrl($locale) }}" class="hover:text-primary transition-colors font-medium">{{ $categoryTitle }}</a>
                    <span>&rsaquo;</span>
                @endif
                <span class="text-outline truncate max-w-[200px] sm:max-w-xs">{{ $postTitle }}</span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-display-lg font-bold text-on-surface tracking-tight leading-tight break-words">
                {{ $postTitle }}
            </h1>

            {{-- Post Meta (Author, Date, Reading Time, Views) --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4 sm:py-6 my-4 sm:my-6 border-y border-surface-container text-sm text-on-surface-variant font-label-sm">
                @if($post->author)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center font-bold text-base shadow-sm flex-shrink-0">
                            {{ substr($post->author->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-on-surface">{{ $post->author->name }}</p>
                            <p class="text-xs text-on-surface-variant">{{ __('blog.written_by') }}</p>
                        </div>
                    </div>
                @endif

                <div class="flex items-center space-x-4 text-xs sm:text-sm text-on-surface-variant">
                    @if($post->published_at)
                        <span class="flex items-center space-x-1.5">
                            <span class="material-symbols-outlined text-base">calendar_today</span>
                            <time datetime="{{ $post->published_at->toIso8601String() }}">
                                {{ $post->published_at->translatedFormat('d. M Y.') }}
                            </time>
                        </span>
                    @endif

                    <span class="flex items-center space-x-1.5">
                        <span class="material-symbols-outlined text-base">schedule</span>
                        <span>{{ $post->reading_time }} {{ __('blog.min_read') }}</span>
                    </span>
                </div>
            </div>

            {{-- Featured Image --}}
            @if($imageUrl)
                <div class="rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm mb-8 sm:mb-10 aspect-video bg-surface-container border border-surface-container-high/60">
                    <img src="{{ $imageUrl }}" alt="{{ $postTitle }}" class="w-full h-full object-cover" />
                </div>
            @endif

            {{-- Post Body --}}
            <div class="rounded-2xl sm:rounded-3xl bg-surface-container-lowest border border-surface-container-high/60 p-5 sm:p-space-8 md:p-space-12 shadow-sm prose prose-slate max-w-none text-on-surface font-body-md leading-relaxed break-words">
                {!! $postBody !!}
            </div>

            {{-- Related Posts --}}
            @if($relatedPosts->isNotEmpty())
                <div class="mt-12 sm:mt-16 pt-8 sm:pt-12 border-t border-surface-container">
                    <h2 class="text-xl sm:text-2xl font-display-lg font-bold text-on-surface mb-6 sm:mb-8">{{ __('blog.related_posts') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($relatedPosts as $related)
                            @php
                                $relTitle = $related->getTranslation('title', $locale, true);
                                $relUrl = $related->getUrl($locale);
                                $relImg = $related->featuredImage?->url ?? null;
                            @endphp
                            <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-surface-container-high/60 flex flex-col justify-between group hover:shadow-md transition-all">
                                @if($relImg)
                                    <a href="{{ $relUrl }}" class="aspect-video overflow-hidden block bg-surface-container">
                                        <img src="{{ $relImg }}" alt="{{ $relTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                    </a>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-on-surface text-sm line-clamp-2 group-hover:text-primary transition-colors">
                                        <a href="{{ $relUrl }}">{{ $relTitle }}</a>
                                    </h3>
                                    @if($related->published_at)
                                        <p class="mt-2 text-xs text-on-surface-variant">{{ $related->published_at->translatedFormat('d. M Y.') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </article>
    </main>

    @include('partials.footer')

</body>
</html>
