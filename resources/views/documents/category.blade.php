<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-frontend-theme="{{ setting('frontend_theme', 'ocean') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $catTitle = $category->getTranslation('title', $locale, true);
        $catDesc = $category->getTranslation('description', $locale, true);
    @endphp

    <title>{{ $catTitle }} - {{ __('documents.archive_title') }}</title>

    <link rel="canonical" href="{{ $category->getUrl($locale) }}" />

    @foreach(['sr', 'en', 'it'] as $lang)
        @php $langSlug = $category->getTranslation('slug', $lang); @endphp
        @if(!empty($langSlug))
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ url("/{$lang}/documents/category/{$langSlug}") }}" />
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

    @include('partials.nav')

    <main class="flex-grow py-space-8 sm:py-space-12">
        <div class="max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="mb-6 p-4 bg-error-container/20 border border-error/30 text-error rounded-2xl text-sm flex items-center gap-3">
                    <span class="material-symbols-outlined text-xl text-error">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header & Search --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 pb-6 sm:pb-8 border-b border-surface-container">
                <div>
                    {{-- Breadcrumb --}}
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-on-surface-variant mb-2 font-label-sm">
                        <a href="{{ url("/{$locale}/documents") }}" class="hover:text-primary transition-colors">{{ __('documents.archive_title') }}</a>
                        <span>&rsaquo;</span>
                        <span class="text-outline">{{ $catTitle }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-display-lg font-bold text-on-surface tracking-tight">
                        {{ $catTitle }}
                    </h1>
                    @if($catDesc)
                        <p class="mt-2 text-on-surface-variant font-body-md max-w-2xl text-sm sm:text-base">
                            {{ $catDesc }}
                        </p>
                    @endif
                </div>

                {{-- Search Form --}}
                <form action="{{ $category->getUrl($locale) }}" method="GET" class="w-full md:w-80">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? request('search') }}" placeholder="{{ __('documents.search_documents') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest text-on-surface text-sm placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Categories Filter Pills --}}
            @if($categories->isNotEmpty())
                <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto py-4 sm:py-6 -mx-gutter-md px-gutter-md sm:mx-0 sm:px-0 scrollbar-none">
                    <a href="{{ url("/{$locale}/documents") }}"
                       class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container border border-surface-container-high/60 shadow-sm">
                        {{ __('documents.all_documents') }}
                    </a>
                    @foreach($categories as $cat)
                        @php
                            $cTitle = $cat->getTranslation('title', $locale, true);
                            $isActive = $cat->id === $category->id;
                        @endphp
                        <a href="{{ $cat->getUrl($locale) }}"
                           class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap transition-all {{ $isActive ? 'bg-primary text-on-primary font-semibold shadow-md' : 'bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container border border-surface-container-high/60 shadow-sm' }}">
                            {{ $cTitle }} ({{ $cat->documents_count }})
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Documents Grid --}}
            @if($documents->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
                    @foreach($documents as $doc)
                        @php
                            $ext = strtolower($doc->file_type ?? pathinfo($doc->file_name, PATHINFO_EXTENSION));
                            $docTitle = $doc->getTranslation('title', $locale, true) ?: $doc->file_name;
                            $docD = $doc->getTranslation('description', $locale, true);
                            $isPdf = in_array($ext, ['pdf']);
                        @endphp
                        <div class="bg-surface-container-lowest rounded-2xl p-5 sm:p-6 border border-surface-container-high/60 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-surface-container text-primary">
                                        {{ $ext ?: 'FILE' }}
                                    </span>
                                    @if($doc->version)
                                        <span class="text-xs font-medium text-on-surface-variant bg-surface-container-low border border-surface-container px-2 py-0.5 rounded-md">
                                            v{{ $doc->version }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-on-surface group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $docTitle }}
                                </h3>

                                @if($docD)
                                    <p class="mt-2 text-xs sm:text-sm text-on-surface-variant line-clamp-2">
                                        {{ $docD }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-6 pt-4 border-t border-surface-container">
                                <div class="flex items-center justify-between text-xs text-on-surface-variant mb-4">
                                    <span>{{ $doc->formatted_size }}</span>
                                    <span>{{ $doc->downloads_count }} {{ __('documents.downloads') }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ $doc->getDownloadUrl($locale) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 bg-primary hover:bg-primary-container text-on-primary text-xs font-semibold rounded-xl transition shadow-sm">
                                        <span class="material-symbols-outlined text-base">download</span>
                                        <span>{{ __('documents.download') }}</span>
                                    </a>
                                    @if($isPdf)
                                        <a href="{{ $doc->getPreviewUrl($locale) }}" target="_blank"
                                           class="inline-flex items-center justify-center p-2.5 bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold rounded-xl transition"
                                           title="{{ __('documents.preview') }}">
                                            <span class="material-symbols-outlined text-base">visibility</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-8 sm:mt-12">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="bg-surface-container-lowest rounded-3xl p-12 text-center border border-surface-container-high/60 shadow-sm mt-6">
                    <div class="inline-flex p-4 bg-surface-container rounded-full text-primary mb-4">
                        <span class="material-symbols-outlined text-3xl">folder_open</span>
                    </div>
                    <h3 class="text-lg font-bold text-on-surface">{{ __('documents.no_documents') }}</h3>
                </div>
            @endif

        </div>
    </main>

    @include('partials.footer')

</body>
</html>
