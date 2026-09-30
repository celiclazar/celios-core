<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'failed_at' => 'datetime',
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
     * Get a short summary of the exception (first line).
     */
    public function getExceptionSummaryAttribute(): string
    {
        if (empty($this->exception)) {
            return 'No exception message';
        }

        $lines = explode("\n", trim($this->exception));

        return $lines[0] ?? 'Unknown error';
    }

    /**
     * Get the formatted exception stack trace.
     */
    public function getExceptionTraceAttribute(): string
    {
        return (string) $this->exception;
    }

    /**
     * Get formatted JSON payload.
     */
    public function getFormattedPayloadAttribute(): string
    {
        $decoded = json_decode($this->payload ?? '{}', true);

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: ($this->payload ?? '');
    }
}
