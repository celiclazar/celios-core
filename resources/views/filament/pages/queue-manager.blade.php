<x-filament-panels::page>
    {{-- Diagnostics & Stats Header --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- Driver Status --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Queue Driver</div>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider {{ ($stats['is_connected'] ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300' }}">
                    {{ $stats['active_driver'] ?? 'unknown' }}
                </span>
                <span class="inline-block w-2 h-2 rounded-full {{ ($stats['is_connected'] ?? false) ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
            </div>
            <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 truncate" title="{{ $stats['connection_message'] ?? '' }}">
                {{ $stats['connection_message'] ?? '' }}
            </div>
        </div>

        {{-- Pending Jobs --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Pending Jobs</div>
            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                {{ $stats['total_pending'] ?? 0 }}
            </div>
            <div class="text-[11px] text-gray-400 mt-1">Across detected queues</div>
        </div>

        {{-- Failed Jobs --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Failed Jobs</div>
            <div class="text-2xl font-bold mt-1 {{ ($stats['total_failed'] ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                {{ $stats['total_failed'] ?? 0 }}
            </div>
            <div class="text-[11px] text-gray-400 mt-1">
                {{ ($stats['total_failed'] ?? 0) > 0 ? 'Requires developer review' : 'No failed jobs' }}
            </div>
        </div>

        {{-- Batches --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Job Batches</div>
            <div class="text-2xl font-bold text-gray-700 dark:text-gray-200 mt-1">
                {{ $stats['total_batches'] ?? 0 }}
            </div>
            <div class="text-[11px] text-gray-400 mt-1">Tracked in database</div>
        </div>

        {{-- Last Restart --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">Worker Restart Signal</div>
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-1">
                {{ $stats['last_restart'] ?? 'None recorded' }}
            </div>
            <div class="text-[11px] text-gray-400 mt-1">Via queue:restart</div>
        </div>
    </div>

    {{-- Tabs Bar --}}
    <div class="border-b border-gray-200 dark:border-gray-800 mb-6 flex space-x-2">
        <button
            type="button"
            wire:click="setTab('failed')"
            class="px-4 py-2.5 text-sm font-medium border-b-2 flex items-center gap-2 transition-colors {{ $activeTab === 'failed' ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <span>Failed Jobs</span>
            @if(($stats['total_failed'] ?? 0) > 0)
                <span class="px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300">
                    {{ $stats['total_failed'] }}
                </span>
            @else
                <span class="px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    0
                </span>
            @endif
        </button>

        <button
            type="button"
            wire:click="setTab('pending')"
            class="px-4 py-2.5 text-sm font-medium border-b-2 flex items-center gap-2 transition-colors {{ $activeTab === 'pending' ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <span>Pending & In-Flight</span>
            <span class="px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                {{ $stats['total_pending'] ?? 0 }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setTab('batches')"
            class="px-4 py-2.5 text-sm font-medium border-b-2 flex items-center gap-2 transition-colors {{ $activeTab === 'batches' ? 'border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <span>Job Batches</span>
            <span class="px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                {{ $stats['total_batches'] ?? 0 }}
            </span>
        </button>
    </div>

    {{-- Tab Content --}}
    <div wire:key="tab-content-{{ $activeTab }}">
        @if($activeTab === 'failed')
            @livewire(\Celios\Core\Filament\Widgets\Queue\FailedJobsTableWidget::class, key('tab-widget-failed'))
        @elseif($activeTab === 'pending')
            @livewire(\Celios\Core\Filament\Widgets\Queue\PendingJobsTableWidget::class, key('tab-widget-pending'))
        @elseif($activeTab === 'batches')
            @livewire(\Celios\Core\Filament\Widgets\Queue\JobBatchesTableWidget::class, key('tab-widget-batches'))
        @endif
    </div>
</x-filament-panels::page>
