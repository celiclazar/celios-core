<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $currentCategory ? ($currentCategory->getTranslation('title', $locale, true) . ' - ' . __('blog.blog')) : __('blog.blog') }}</title>

    <link rel="canonical" href="{{ $currentCategory ? $currentCategory->getUrl($locale) : url("/{$locale}/blog") }}" />

    @foreach(['sr', 'en', 'it'] as $lang)
        @if($currentCategory)
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ $currentCategory->getUrl($lang) }}" />
        @else
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ url("/{$lang}/blog") }}" />
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

    @include('partials.nav', ['currentCategory' => $currentCategory, 'isBlogIndex' => !$currentCategory])

    <main class="flex-grow py-space-8 sm:py-space-12">
        <div class="max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg">

            {{-- Header & Search --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 sm:pb-8 border-b border-surface-container">
                <div>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-display-lg font-bold text-on-surface tracking-tight">
                        {{ $currentCategory ? $currentCategory->getTranslation('title', $locale, true) : __('blog.all_articles') }}
                    </h1>
                    @if($currentCategory && filled($currentCategory->getTranslation('description', $locale, true)))
                        <p class="mt-2 text-on-surface-variant font-body-md max-w-2xl text-sm sm:text-base">
                            {{ $currentCategory->getTranslation('description', $locale, true) }}
                        </p>
                    @endif
                </div>

                {{-- Search Form --}}
                <form action="{{ $currentCategory ? $currentCategory->getUrl($locale) : url("/{$locale}/blog") }}" method="GET" class="w-full md:w-80">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('blog.search_articles') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest text-on-surface text-sm placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Category Filter Pills --}}
            @if($categories->isNotEmpty())
                <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto py-4 sm:py-6 -mx-gutter-md px-gutter-md sm:mx-0 sm:px-0 scrollbar-none">
                    <a href="{{ url("/{$locale}/blog") }}"
                       class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all {{ !$currentCategory ? 'bg-primary text-on-primary font-semibold shadow-md' : 'bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container border border-surface-container-high/60 shadow-sm' }}">
                        {{ __('blog.all_categories') }}
                    </a>
                    @foreach($categories as $category)
                        @php
                            $catTitle = $category->getTranslation('title', $locale, true);
                            $isActive = $currentCategory && $currentCategory->id === $category->id;
                        @endphp
                        <a href="{{ $category->getUrl($locale) }}"
                           class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all {{ $isActive ? 'bg-primary text-on-primary font-semibold shadow-md' : 'bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container border border-surface-container-high/60 shadow-sm' }}">
                            {{ $catTitle }} ({{ $category->posts_count }})
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Articles Grid --}}
            @if($posts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-6 mt-4 sm:mt-6">
                    @foreach($posts as $post)
                        @php
                            $postTitle = $post->getTranslation('title', $locale, true);
                            $postExcerpt = $post->getTranslation('excerpt', $locale, true);
                            $postUrl = $post->getUrl($locale);
                            $catTitle = $post->category?->getTranslation('title', $locale, true);
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

                            <div class="flex-1 p-4 sm:p-space-6 flex flex-col justify-between">
                                <div>
                                    @if($catTitle)
                                        <div class="mb-3">
                                            <a href="{{ $post->category->getUrl($locale) }}" class="inline-flex items-center px-3 py-1 rounded-full font-label-xs text-label-xs font-semibold bg-surface-container text-primary hover:bg-surface-container-high transition-colors">
                                                {{ $catTitle }}
                                            </a>
                                        </div>
                                    @endif

                                    <h2 class="font-headline-md text-lg sm:text-xl font-bold text-on-surface line-clamp-2 group-hover:text-primary transition-colors">
                                        <a href="{{ $postUrl }}">
                                            {{ $postTitle }}
                                        </a>
                                    </h2>

                                    @if(filled($postExcerpt))
                                        <p class="mt-2 sm:mt-3 font-body-sm text-body-sm text-on-surface-variant line-clamp-3 leading-relaxed">
                                            {{ $postExcerpt }}
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-4 sm:mt-6 pt-4 border-t border-surface-container flex flex-wrap items-center justify-between gap-2 font-label-xs text-label-xs text-on-surface-variant">
                                    <div class="flex items-center space-x-2">
                                        @if($post->author)
                                            <span class="font-medium text-on-surface">{{ $post->author->name }}</span>
                                        @endif
                                        @if($post->author && $post->published_at)
                                            <span>•</span>
                                        @endif
                                        @if($post->published_at)
                                            <time datetime="{{ $post->published_at->toIso8601String() }}">
                                                {{ $post->published_at->translatedFormat('d. M Y.') }}
                                            </time>
                                        @endif
                                    </div>

                                    <a href="{{ $postUrl }}" class="font-semibold text-primary hover:text-primary-dim inline-flex items-center gap-1">
                                        <span>{{ __('blog.read_more') ?? 'Čitaj dalje' }}</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="bg-surface-container-lowest rounded-3xl p-12 text-center border border-surface-container-high/60 shadow-sm mt-6">
                    <div class="inline-flex p-4 bg-surface-container rounded-full text-primary mb-4">
                        <span class="material-symbols-outlined text-3xl">sentiment_dissatisfied</span>
                    </div>
                    <h3 class="text-lg font-bold text-on-surface">{{ __('blog.no_posts_found') }}</h3>
                </div>
            @endif

        </div>
    </main>

    @include('partials.footer')

</body>
</html>
