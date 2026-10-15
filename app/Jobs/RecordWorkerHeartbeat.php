<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecordWorkerHeartbeat implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Cache::put(
            config('watchdog.health.worker_key'),
            now()->timestamp,
            config('watchdog.health.retention_seconds'),
        );

        Log::info('watchdog.worker.heartbeat');
    }
}
