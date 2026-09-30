<?php

namespace Celios\Core\Jobs\Developer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TestQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $behavior;

    public string $message;

    /**
     * Create a new job instance.
     *
     * @param  string  $behavior  'success', 'fail', or 'delayed'
     * @param  string  $message  Custom diagnostic message
     */
    public function __construct(string $behavior = 'success', string $message = 'Test job dispatched by developer')
    {
        $this->behavior = $behavior;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put('developer:last_test_job_processed_at', now()->toDateTimeString(), 86400);

        if ($this->behavior === 'fail') {
            Log::warning("[Developer Queue Test] Deliberate test failure triggered: {$this->message}");
            throw new RuntimeException("Developer test job exception: {$this->message}");
        }

        Log::info("[Developer Queue Test] Test job executed successfully: {$this->message}");
    }
}
