@php
    $locale = app()->getLocale();
    $heading = $data['heading'][$locale] ?? ($data['heading'][config('locales.default', 'sr')] ?? (is_string($data['heading'] ?? null) ? $data['heading'] : null));
    $subtitle = $data['subtitle'][$locale] ?? ($data['subtitle'][config('locales.default', 'sr')] ?? (is_string($data['subtitle'] ?? null) ? $data['subtitle'] : null));
    $mode = $data['selection_mode'] ?? 'category';
    $categoryId = $data['category_id'] ?? null;
    $documentIds = $data['document_ids'] ?? [];
    $limit = (int) ($data['limit'] ?? 6);
    $layout = $data['layout'] ?? 'grid';
    $showSize = $data['show_size'] ?? true;
    $showVersion = $data['show_version'] ?? true;
    $showDate = $data['show_date'] ?? true;
    $showDownloads = $data['show_downloads'] ?? false;

    $query = \App\Models\Document::query()
        ->published()
        ->visibleToUser(auth()->user())
        ->with('category');

    if ($mode === 'manual' && !empty($documentIds)) {
        $query->whereIn('id', $documentIds);
    } elseif ($categoryId) {
        $query->where('category_id', $categoryId);
    }

    $documents = $query->orderBy('created_at', 'desc')->limit($limit)->get();
@endphp

@if($documents->isNotEmpty())
<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="documents">
    @if($heading || $subtitle)
        <div class="text-center max-w-3xl mx-auto mb-space-8 sm:mb-space-12">
            @if($heading)
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-display-lg font-bold text-on-surface tracking-tight break-words">
                    {{ $heading }}
                </h2>
            @endif
            @if($subtitle)
                <p class="font-body-md text-body-md text-on-surface-variant mt-2 sm:mt-3 leading-relaxed text-sm sm:text-base">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif

    @if($layout === 'table')
        {{-- Compact Table Layout --}}
        <div class="overflow-x-auto rounded-2xl border border-surface-container-high/60 shadow-sm bg-surface-container-lowest -mx-gutter-md px-gutter-md sm:mx-0 sm:px-0">
            <table class="min-w-full divide-y divide-surface-container text-sm">
                <thead class="bg-surface-container-low text-on-surface font-semibold font-label-sm">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-left">{{ __('fields.title') ?? 'Naziv' }}</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-left">{{ __('documents.file_type') ?? 'Tip' }}</th>
                        @if($showSize)
                            <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-left">{{ __('documents.file_size') ?? 'Veličina' }}</th>
                        @endif
                        @if($showVersion)
                            <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-left">{{ __('documents.version') ?? 'Verzija' }}</th>
                        @endif
                        @if($showDate)
                            <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-left">{{ __('fields.created_at') ?? 'Datum' }}</th>
                        @endif
                        @if($showDownloads)
                            <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-left">{{ __('documents.downloads') ?? 'Preuzimanja' }}</th>
                        @endif
                        <th scope="col" class="px-4 sm:px-6 py-3 sm:py-4 text-right">{{ __('actions.actions') ?? 'Akcije' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container/60">
                    @foreach($documents as $doc)
                        @php
                            $ext = strtolower($doc->file_type ?? pathinfo($doc->file_name, PATHINFO_EXTENSION));
                            $isPdf = in_array($ext, ['pdf']);
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-on-surface">
                                {{ $doc->getTranslation('title', $locale, true) ?: $doc->file_name }}
                                @if($doc->category)
                                    <span class="block font-label-xs text-xs font-normal text-on-surface-variant mt-0.5">
                                        {{ $doc->category->getTranslation('title', $locale, true) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-surface-container text-primary">
                                    {{ $ext ?: 'FILE' }}
                                </span>
                            </td>
                            @if($showSize)
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-on-surface-variant text-xs">{{ $doc->formatted_size }}</td>
                            @endif
                            @if($showVersion)
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-on-surface-variant text-xs">{{ $doc->version ? 'v' . $doc->version : '-' }}</td>
                            @endif
                            @if($showDate)
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-on-surface-variant text-xs">{{ $doc->created_at->format('d.m.Y') }}</td>
                            @endif
                            @if($showDownloads)
                                <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-on-surface-variant text-xs">{{ $doc->downloads_count }}</td>
                            @endif
                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-right space-x-2">
                                @if($isPdf)
                                    <a href="{{ $doc->getPreviewUrl($locale) }}" target="_blank"
                                       class="inline-flex items-center px-2.5 sm:px-3 py-1.5 rounded-lg border border-surface-container-high bg-surface-container-lowest text-xs font-medium text-on-surface hover:bg-surface-container transition">
                                        {{ __('documents.preview') ?? 'Pregled' }}
                                    </a>
                                @endif
                                <a href="{{ $doc->getDownloadUrl($locale) }}"
                                   class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-xs font-medium text-on-primary transition shadow-sm">
                                    <span class="material-symbols-outlined text-sm">download</span>
                                    <span>{{ __('documents.download') ?? 'Preuzmi' }}</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @elseif($layout === 'list')
        {{-- Detailed List Layout --}}
        <div class="space-y-4 max-w-4xl mx-auto">
            @foreach($documents as $doc)
                @php
                    $ext = strtolower($doc->file_type ?? pathinfo($doc->file_name, PATHINFO_EXTENSION));
                    $isPdf = in_array($ext, ['pdf']);
                @endphp
                <div class="bg-surface-container-lowest rounded-2xl p-4 sm:p-5 border border-surface-container-high/60 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3 sm:gap-4">
                        <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-surface-container text-primary font-bold uppercase text-xs flex-shrink-0">
                            {{ $ext ?: 'FILE' }}
                        </span>
                        <div>
                            <h3 class="font-bold text-on-surface text-sm sm:text-base">
                                {{ $doc->getTranslation('title', $locale, true) ?: $doc->file_name }}
                            </h3>
                            @if($doc->getTranslation('description', $locale, true))
                                <p class="text-xs text-on-surface-variant mt-1 line-clamp-1">
                                    {{ $doc->getTranslation('description', $locale, true) }}
                                </p>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-on-surface-variant/80 mt-2">
                                @if($showSize) <span>{{ $doc->formatted_size }}</span> @endif
                                @if($showVersion && $doc->version) <span>&bull; v{{ $doc->version }}</span> @endif
                                @if($showDate) <span>&bull; {{ $doc->created_at->format('d.m.Y') }}</span> @endif
                                @if($showDownloads) <span>&bull; {{ $doc->downloads_count }} {{ __('documents.downloads') ?? 'preuzimanja' }}</span> @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                        @if($isPdf)
                            <a href="{{ $doc->getPreviewUrl($locale) }}" target="_blank"
                               class="flex-1 sm:flex-initial inline-flex items-center justify-center px-4 py-2 bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold rounded-xl transition text-center">
                                {{ __('documents.preview') ?? 'Pregled' }}
                            </a>
                        @endif
                        <a href="{{ $doc->getDownloadUrl($locale) }}"
                           class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-container text-on-primary text-xs font-semibold rounded-xl transition shadow-sm text-center">
                            <span class="material-symbols-outlined text-base">download</span>
                            <span>{{ __('documents.download') ?? 'Preuzmi' }}</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Grid Cards Layout --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($documents as $doc)
                @php
                    $ext = strtolower($doc->file_type ?? pathinfo($doc->file_name, PATHINFO_EXTENSION));
                    $isPdf = in_array($ext, ['pdf']);
                @endphp
                <div class="bg-surface-container-lowest rounded-2xl p-5 sm:p-6 border border-surface-container-high/60 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-surface-container text-primary">
                                {{ $ext ?: 'FILE' }}
                            </span>
                            @if($showVersion && $doc->version)
                                <span class="text-xs font-medium text-on-surface-variant bg-surface-container-low border border-surface-container px-2 py-0.5 rounded-md">
                                    v{{ $doc->version }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base sm:text-lg font-bold text-on-surface group-hover:text-primary transition-colors line-clamp-2">
                            {{ $doc->getTranslation('title', $locale, true) ?: $doc->file_name }}
                        </h3>

                        @if($doc->getTranslation('description', $locale, true))
                            <p class="mt-2 text-xs sm:text-sm text-on-surface-variant line-clamp-2">
                                {{ $doc->getTranslation('description', $locale, true) }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-surface-container">
                        <div class="flex items-center justify-between text-xs text-on-surface-variant mb-4">
                            @if($showSize) <span>{{ $doc->formatted_size }}</span> @endif
                            @if($showDownloads) <span>{{ $doc->downloads_count }} {{ __('documents.downloads') ?? 'preuzimanja' }}</span> @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ $doc->getDownloadUrl($locale) }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 bg-primary hover:bg-primary-container text-on-primary text-xs font-semibold rounded-xl transition shadow-sm">
                                <span class="material-symbols-outlined text-base">download</span>
                                <span>{{ __('documents.download') ?? 'Preuzmi' }}</span>
                            </a>
                            @if($isPdf)
                                <a href="{{ $doc->getPreviewUrl($locale) }}" target="_blank"
                                   class="inline-flex items-center justify-center p-2.5 bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold rounded-xl transition"
                                   title="{{ __('documents.preview') ?? 'Pregled' }}">
                                    <span class="material-symbols-outlined text-base">visibility</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endif
