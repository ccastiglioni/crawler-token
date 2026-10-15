<?php

use App\Jobs\RecordWorkerHeartbeat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new RecordWorkerHeartbeat, config('watchdog.health.queue'))
    ->name('watchdog:heartbeat')
    ->everyMinute()
    ->withoutOverlapping()
    ->after(function (): void {
        Cache::put(
            config('watchdog.health.scheduler_key'),
            now()->timestamp,
            config('watchdog.health.retention_seconds'),
        );
    });
