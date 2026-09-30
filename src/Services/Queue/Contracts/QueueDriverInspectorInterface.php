<?php

namespace Celios\Core\Services\Queue\Contracts;

use Illuminate\Support\Collection;

interface QueueDriverInspectorInterface
{
    /**
     * Get the driver identifier (e.g. 'database', 'redis').
     */
    public function getDriverName(): string;

    /**
     * Check if connection to the queue driver is healthy and available.
     */
    public function isAvailable(): bool;

    /**
     * Get connection diagnostic information.
     */
    public function getDiagnostics(): array;

    /**
     * Get pending and reserved jobs from a queue.
     *
     * @return Collection<int, array>
     */
    public function getPendingJobs(string $queue = 'default', int $limit = 50): Collection;

    /**
     * Delete a pending job by its ID or identifier.
     */
    public function deletePendingJob(string|int $id, string $queue = 'default'): bool;

    /**
     * Get the number of pending jobs in a queue.
     */
    public function getQueueSize(string $queue = 'default'): int;

    /**
     * Get known queue names for this driver.
     *
     * @return array<int, string>
     */
    public function getQueueNames(): array;
}
