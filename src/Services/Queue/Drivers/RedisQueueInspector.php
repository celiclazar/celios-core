<?php

namespace Celios\Core\Services\Queue\Drivers;

use Celios\Core\Services\Queue\Contracts\QueueDriverInspectorInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisQueueInspector implements QueueDriverInspectorInterface
{
    protected string $connectionName;

    public function __construct(?string $connectionName = null)
    {
        $this->connectionName = $connectionName
            ?? config('queue.connections.redis.connection', 'default');
    }

    public function getDriverName(): string
    {
        return 'redis';
    }

    public function isAvailable(): bool
    {
        try {
            $redis = Redis::connection($this->connectionName);
            $pong = $redis->ping();

            return $pong === true || $pong === '+PONG' || $pong === 'PONG';
        } catch (Throwable) {
            return false;
        }
    }

    public function getDiagnostics(): array
    {
        $connected = $this->isAvailable();
        $host = config('database.redis.default.host', '127.0.0.1');
        $port = config('database.redis.default.port', '6379');
        $client = config('database.redis.client', 'phpredis');

        return [
            'driver' => 'redis',
            'connected' => $connected,
            'client' => $client,
            'host' => "{$host}:{$port}",
            'connection_name' => $this->connectionName,
            'message' => $connected ? "Redis connected ({$client})" : "Cannot connect to Redis at {$host}:{$port}",
        ];
    }

    public function getPendingJobs(string $queue = 'default', int $limit = 50): Collection
    {
        if (! $this->isAvailable()) {
            return collect();
        }

        try {
            $redis = Redis::connection($this->connectionName);
            $jobs = collect();

            // 1. Reserved jobs (in flight) from Sorted Set
            $reservedRaw = $redis->zrangebyscore("queues:{$queue}:reserved", '-inf', '+inf', [
                'withscores' => true,
                'limit' => [0, $limit],
            ]);

            if (is_array($reservedRaw)) {
                foreach ($reservedRaw as $rawPayload => $score) {
                    $payload = json_decode((string) $rawPayload, true) ?: [];
                    $jobs->push($this->formatRedisJob(
                        id: $payload['id'] ?? md5($rawPayload),
                        queue: $queue,
                        payload: $payload,
                        rawPayload: (string) $rawPayload,
                        isReserved: true,
                        reservedAt: (int) $score
                    ));
                }
            }

            // 2. Immediate waiting jobs from List
            $remainingLimit = max(0, $limit - $jobs->count());
            if ($remainingLimit > 0) {
                $rawList = $redis->lrange("queues:{$queue}", 0, $remainingLimit - 1);
                if (is_array($rawList)) {
                    foreach ($rawList as $rawPayload) {
                        $payload = json_decode((string) $rawPayload, true) ?: [];
                        $jobs->push($this->formatRedisJob(
                            id: $payload['id'] ?? md5($rawPayload),
                            queue: $queue,
                            payload: $payload,
                            rawPayload: (string) $rawPayload,
                            isReserved: false
                        ));
                    }
                }
            }

            // 3. Delayed jobs from Sorted Set
            $remainingDelayedLimit = max(0, $limit - $jobs->count());
            if ($remainingDelayedLimit > 0) {
                $delayedRaw = $redis->zrangebyscore("queues:{$queue}:delayed", '-inf', '+inf', [
                    'withscores' => true,
                    'limit' => [0, $remainingDelayedLimit],
                ]);

                if (is_array($delayedRaw)) {
                    foreach ($delayedRaw as $rawPayload => $score) {
                        $payload = json_decode((string) $rawPayload, true) ?: [];
                        $jobs->push($this->formatRedisJob(
                            id: $payload['id'] ?? md5($rawPayload),
                            queue: $queue,
                            payload: $payload,
                            rawPayload: (string) $rawPayload,
                            isReserved: false,
                            availableAt: (int) $score
                        ));
                    }
                }
            }

            return $jobs;
        } catch (Throwable) {
            return collect();
        }
    }

    public function deletePendingJob(string|int $id, string $queue = 'default'): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        try {
            $redis = Redis::connection($this->connectionName);

            // Search in active list
            $listItems = $redis->lrange("queues:{$queue}", 0, -1);
            if (is_array($listItems)) {
                foreach ($listItems as $item) {
                    $decoded = json_decode((string) $item, true);
                    if (($decoded['id'] ?? null) == $id) {
                        $redis->lrem("queues:{$queue}", 1, $item);

                        return true;
                    }
                }
            }

            // Search in delayed zset
            $delayedItems = $redis->zrangebyscore("queues:{$queue}:delayed", '-inf', '+inf');
            if (is_array($delayedItems)) {
                foreach ($delayedItems as $item) {
                    $decoded = json_decode((string) $item, true);
                    if (($decoded['id'] ?? null) == $id) {
                        $redis->zrem("queues:{$queue}:delayed", $item);

                        return true;
                    }
                }
            }

            // Search in reserved zset
            $reservedItems = $redis->zrangebyscore("queues:{$queue}:reserved", '-inf', '+inf');
            if (is_array($reservedItems)) {
                foreach ($reservedItems as $item) {
                    $decoded = json_decode((string) $item, true);
                    if (($decoded['id'] ?? null) == $id) {
                        $redis->zrem("queues:{$queue}:reserved", $item);

                        return true;
                    }
                }
            }

            return false;
        } catch (Throwable) {
            return false;
        }
    }

    public function getQueueSize(string $queue = 'default'): int
    {
        if (! $this->isAvailable()) {
            return 0;
        }

        try {
            $redis = Redis::connection($this->connectionName);
            $size = (int) $redis->llen("queues:{$queue}");
            $delayed = (int) $redis->zcard("queues:{$queue}:delayed");
            $reserved = (int) $redis->zcard("queues:{$queue}:reserved");

            return $size + $delayed + $reserved;
        } catch (Throwable) {
            return 0;
        }
    }

    public function getQueueNames(): array
    {
        return ['default'];
    }

    protected function formatRedisJob(
        string $id,
        string $queue,
        array $payload,
        string $rawPayload,
        bool $isReserved = false,
        ?int $reservedAt = null,
        ?int $availableAt = null
    ): array {
        $jobName = $payload['displayName']
            ?? $payload['data']['commandName']
            ?? 'Unknown Job';

        return [
            'id' => $id,
            'queue' => $queue,
            'job_name' => $jobName,
            'attempts' => $payload['attempts'] ?? 0,
            'is_reserved' => $isReserved,
            'reserved_at' => $reservedAt ? Carbon::createFromTimestamp($reservedAt)->toDateTimeString() : null,
            'available_at' => $availableAt ? Carbon::createFromTimestamp($availableAt)->toDateTimeString() : null,
            'created_at' => null,
            'payload' => $payload,
            'formatted_payload' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];
    }
}
