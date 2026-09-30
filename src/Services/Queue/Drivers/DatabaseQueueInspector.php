<?php

namespace Celios\Core\Services\Queue\Drivers;

use Celios\Core\Models\QueueJob;
use Celios\Core\Services\Queue\Contracts\QueueDriverInspectorInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseQueueInspector implements QueueDriverInspectorInterface
{
    public function getDriverName(): string
    {
        return 'database';
    }

    public function isAvailable(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function getDiagnostics(): array
    {
        $connected = $this->isAvailable();

        return [
            'driver' => 'database',
            'connected' => $connected,
            'connection_name' => config('queue.connections.database.connection') ?? config('database.default'),
            'table' => config('queue.connections.database.table', 'jobs'),
            'message' => $connected ? 'Database connection active' : 'Unable to connect to database',
        ];
    }

    public function getPendingJobs(string $queue = 'default', int $limit = 50): Collection
    {
        try {
            $query = QueueJob::query();

            if (! empty($queue)) {
                $query->where('queue', $queue);
            }

            return $query->orderBy('id', 'asc')
                ->limit($limit)
                ->get()
                ->map(function (QueueJob $job) {
                    $payload = $job->payload_array;

                    return [
                        'id' => (string) $job->id,
                        'queue' => $job->queue,
                        'job_name' => $job->job_name,
                        'attempts' => $job->attempts,
                        'is_reserved' => $job->is_reserved,
                        'reserved_at' => $job->reserved_at ? Carbon::parse($job->reserved_at)->toDateTimeString() : null,
                        'available_at' => $job->available_at ? Carbon::parse($job->available_at)->toDateTimeString() : null,
                        'created_at' => $job->created_at ? Carbon::parse($job->created_at)->toDateTimeString() : null,
                        'payload' => $payload,
                        'formatted_payload' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                    ];
                });
        } catch (Throwable) {
            return collect();
        }
    }

    public function deletePendingJob(string|int $id, string $queue = 'default'): bool
    {
        try {
            return DB::table('jobs')->where('id', $id)->delete() > 0;
        } catch (Throwable) {
            return false;
        }
    }

    public function getQueueSize(string $queue = 'default'): int
    {
        try {
            return DB::table('jobs')->where('queue', $queue)->count();
        } catch (Throwable) {
            return 0;
        }
    }

    public function getQueueNames(): array
    {
        try {
            $names = DB::table('jobs')->distinct()->pluck('queue')->filter()->values()->toArray();

            return ! empty($names) ? $names : ['default'];
        } catch (Throwable) {
            return ['default'];
        }
    }
}
