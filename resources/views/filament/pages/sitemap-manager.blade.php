<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total URLs</div>
            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                {{ $stats['total_urls'] ?? 0 }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">CMS Pages</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $stats['pages_count'] ?? 0 }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Blog Posts</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                {{ $stats['posts_count'] ?? 0 }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Categories</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                {{ $stats['categories_count'] ?? 0 }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Static Routes</div>
            <div class="text-2xl font-bold text-gray-700 dark:text-gray-300 mt-1">
                {{ $stats['static_count'] ?? 0 }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Static File (XML)</div>
            <div class="text-sm font-semibold mt-1 {{ ($stats['has_static_file'] ?? false) ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">
                @if($stats['has_static_file'] ?? false)
                    {{ $stats['static_size'] }}
                @else
                    Not generated
                @endif
            </div>
            @if($stats['static_modified'] ?? false)
                <div class="text-[10px] text-gray-400 mt-0.5">{{ $stats['static_modified'] }}</div>
            @endif
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
