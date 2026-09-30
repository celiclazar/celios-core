<div class="space-y-4">
    <div class="grid grid-cols-2 gap-2 text-sm bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Queue:</span>
            <span class="font-mono text-gray-800 dark:text-gray-200 ml-1">{{ $record->queue }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Failed At:</span>
            <span class="text-gray-800 dark:text-gray-200 ml-1">{{ $record->failed_at?->toDateTimeString() }}</span>
        </div>
        <div class="col-span-2">
            <span class="font-semibold text-gray-500 dark:text-gray-400">UUID:</span>
            <span class="font-mono text-xs text-gray-600 dark:text-gray-300 ml-1">{{ $record->uuid }}</span>
        </div>
    </div>

    <div>
        <div class="text-xs font-semibold uppercase tracking-wider text-red-600 dark:text-red-400 mb-1">
            Exception Message
        </div>
        <div class="p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-lg text-sm text-red-800 dark:text-red-300 font-mono break-words">
            {{ $record->exception_summary }}
        </div>
    </div>

    <div>
        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
            Full Stack Trace
        </div>
        <pre class="bg-gray-950 text-gray-200 p-4 rounded-lg overflow-x-auto text-xs font-mono max-h-80 leading-relaxed">{{ $record->exception_trace }}</pre>
    </div>

    <details class="cursor-pointer">
        <summary class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            View Job Payload (JSON)
        </summary>
        <pre class="mt-2 bg-gray-900 text-emerald-400 p-3 rounded-lg overflow-x-auto text-xs font-mono max-h-60">{{ $record->formatted_payload }}</pre>
    </details>
</div>
