<?php

namespace Tests\Feature;

use App\Jobs\RecordWorkerHeartbeat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InfrastructureTest extends TestCase
{
    public function test_http_health_is_available(): void
    {
        $this->assertSame('array', config('cache.default'));
        $this->assertSame('sync', config('queue.default'));
        $this->get('/health')->assertOk();
    }

    public function test_database_is_the_explicit_test_database_with_queue_failure_storage(): void
    {
        $this->assertSame('crawler_token_test', DB::connection()->getDatabaseName());
        $this->assertSame('mysql_test', DB::connection()->getConfig('host'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
    }

    public function test_scheduler_dispatches_heartbeat_without_executing_it(): void
    {
        Queue::fake();

        $this->artisan('schedule:run')->assertSuccessful();

        Queue::assertPushedOn(config('watchdog.health.queue'), RecordWorkerHeartbeat::class);
        $this->assertNotNull(Cache::get(config('watchdog.health.scheduler_key')));
        $this->assertNull(Cache::get(config('watchdog.health.worker_key')));
    }

    public function test_full_health_checks_connections_and_accepts_redis_numeric_strings(): void
    {
        Cache::put(config('watchdog.health.scheduler_key'), (string) now()->timestamp);
        Cache::put(config('watchdog.health.worker_key'), (string) now()->timestamp);

        $this->artisan('watchdog:health')->assertSuccessful();

        Cache::put(config('watchdog.health.worker_key'), 'invalid');

        $this->artisan('watchdog:health --component=worker')->assertFailed();
    }

    public function test_worker_records_activity_and_health_becomes_stale(): void
    {
        $this->freezeTime();
        $this->artisan('watchdog:health --component=worker')->assertFailed();

        (new RecordWorkerHeartbeat)->handle();

        $this->assertSame(now()->timestamp, Cache::get(config('watchdog.health.worker_key')));
        $this->artisan('watchdog:health --component=worker')->assertSuccessful();

        $this->travel(config('watchdog.health.max_age_seconds') + 1)->seconds();

        $this->artisan('watchdog:health --component=worker')->assertFailed();
    }

    public function test_health_rejects_invalid_components_and_future_activity(): void
    {
        $this->artisan('watchdog:health --component=unknown')->assertExitCode(2);
        Cache::put(config('watchdog.health.scheduler_key'), now()->addMinute()->timestamp);

        $this->artisan('watchdog:health --component=scheduler')->assertFailed();
    }
}
