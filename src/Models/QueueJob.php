<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;

class QueueJob extends Model
{
    protected $table = 'jobs';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'attempts' => 'integer',
        'reserved_at' => 'datetime',
        'available_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the decoded payload array.
     */
    public function getPayloadArrayAttribute(): array
    {
        return json_decode($this->payload ?? '{}', true) ?: [];
    }

    /**
     * Get the human-readable job name / class.
     */
    public function getJobNameAttribute(): string
    {
        $payload = $this->payload_array;

        return $payload['displayName']
            ?? $payload['data']['commandName']
            ?? 'Unknown Job';
    }

    /**
     * Check if job is currently reserved by a worker.
     */
    public function getIsReservedAttribute(): bool
    {
        return ! is_null($this->reserved_at);
    }
}
