<?php

namespace Celios\Core\Services\Queue;

use Celios\Core\Models\FailedJob;
use Celios\Core\Models\JobBatch;
use Celios\Core\Services\Queue\Contracts\QueueDriverInspectorInterface;
use Celios\Core\Services\Queue\Drivers\DatabaseQueueInspector;
use Celios\Core\Services\Queue\Drivers\RedisQueueInspector;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Throwable;

class QueueInspectorManager
{
    protected array $drivers = [];

    /**
     * Resolve the queue driver inspector instance.
     */
    public function driver(?string $name = null): QueueDriverInspectorInterface
    {
        $name = $name ?: config('queue.default', 'database');

        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = match ($name) {
                'redis' => new RedisQueueInspector,
                default => new DatabaseQueueInspector,
            };
        }

        return $this->drivers[$name];
    }

    /**
     * Get active driver identifier.
     */
    public function getActiveDriverName(): string
    {
        return config('queue.default', 'database');
    }

    /**
     * Get active driver diagnostics.
     */
    public function getDiagnostics(): array
    {
        return $this->driver()->getDiagnostics();
    }

    /**
     * Check if active driver is connected and healthy.
     */
    public function isAvailable(): bool
    {
        return $this->driver()->isAvailable();
    }

    /**
     * Get pending jobs from the active queue driver.
     */
    public function getPendingJobs(string $queue = 'default', int $limit = 50): Collection
    {
        return $this->driver()->getPendingJobs($queue, $limit);
    }

    /**
     * Delete a pending job.
     */
    public function deletePendingJob(string|int $id, string $queue = 'default'): bool
    {
        return $this->driver()->deletePendingJob($id, $queue);
    }

    /**
     * Get all detected queue names.
     */
    public function getQueueNames(): array
    {
        return $this->driver()->getQueueNames();
    }

    /**
     * Get high-level queue metrics.
     */
    public function getStats(): array
    {
        $driver = $this->driver();
        $diagnostics = $driver->getDiagnostics();

        $queues = $driver->getQueueNames();
        $totalPending = 0;
        foreach ($queues as $q) {
            $totalPending += $driver->getQueueSize($q);
        }

        $totalFailed = 0;
        try {
            $totalFailed = FailedJob::count();
        } catch (Throwable) {
            // failed_jobs table might not exist
        }

        $totalBatches = 0;
        try {
            $totalBatches = JobBatch::count();
        } catch (Throwable) {
            // job_batches table might not exist
        }

        $restartTimestamp = Cache::get('illuminate:queue:restart');
        $lastRestart = $restartTimestamp ? Carbon::createFromTimestamp($restartTimestamp)->diffForHumans() : null;

        $hasHorizon = $this->getActiveDriverName() === 'redis' && class_exists('\Laravel\Horizon\Horizon');

        return [
            'active_driver' => $this->getActiveDriverName(),
            'is_connected' => $diagnostics['connected'] ?? false,
            'connection_message' => $diagnostics['message'] ?? '',
            'total_pending' => $totalPending,
            'total_failed' => $totalFailed,
            'total_batches' => $totalBatches,
            'last_restart' => $lastRestart,
            'has_horizon' => $hasHorizon,
            'queues' => $queues,
        ];
    }
}
