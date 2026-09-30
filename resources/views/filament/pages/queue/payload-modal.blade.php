<div class="space-y-4">
    <div class="grid grid-cols-2 gap-2 text-sm bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Queue:</span>
            <span class="font-mono text-gray-800 dark:text-gray-200 ml-1">{{ $record['queue'] ?? 'default' }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Attempts:</span>
            <span class="font-mono text-gray-800 dark:text-gray-200 ml-1">{{ $record['attempts'] ?? 0 }}</span>
        </div>
        <div class="col-span-2">
            <span class="font-semibold text-gray-500 dark:text-gray-400">Job:</span>
            <span class="font-mono text-xs text-primary-600 dark:text-primary-400 ml-1">{{ $record['job_name'] ?? 'Unknown' }}</span>
        </div>
    </div>

    <div>
        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
            Job Payload
        </div>
        <pre class="bg-gray-950 text-emerald-400 p-4 rounded-lg overflow-x-auto text-xs font-mono max-h-96 leading-relaxed">{{ $record['formatted_payload'] ?? json_encode($record['payload'] ?? [], JSON_PRETTY_PRINT) }}</pre>
    </div>
</div>
