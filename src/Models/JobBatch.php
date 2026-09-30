<?php

namespace Celios\Core\Models;

use Illuminate\Database\Eloquent\Model;

class JobBatch extends Model
{
    protected $table = 'job_batches';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'total_jobs' => 'integer',
        'pending_jobs' => 'integer',
        'failed_jobs' => 'integer',
        'failed_job_ids' => 'array',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Calculate progress percentage (0 - 100).
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_jobs <= 0) {
            return 100;
        }

        $processed = $this->total_jobs - $this->pending_jobs;

        return (int) round(($processed / $this->total_jobs) * 100);
    }

    /**
     * Get batch status label.
     */
    public function getStatusAttribute(): string
    {
        if (! is_null($this->cancelled_at)) {
            return 'cancelled';
        }

        if (! is_null($this->finished_at)) {
            return $this->failed_jobs > 0 ? 'finished_with_failures' : 'finished';
        }

        return 'pending';
    }
}
