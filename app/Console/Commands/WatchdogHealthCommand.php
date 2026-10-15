<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class WatchdogHealthCommand extends Command
{
    protected $signature = 'watchdog:health {--component=all : all, scheduler ou worker}';

    protected $description = 'Verifica banco, Redis e atividade recente do scheduler/worker';

    public function handle(): int
    {
        $component = $this->option('component');

        if ($component !== 'all' && $component !== 'scheduler' && $component !== 'worker') {
            $this->error('Componente inválido: use all, scheduler ou worker.');

            return self::INVALID;
        }

        try {
            if ($component === 'all') {
                DB::connection()->getPdo();
                Redis::connection()->ping();
                $this->info('MySQL e Redis: conectados.');
            }

            $healthy = true;

            foreach (['scheduler', 'worker'] as $service) {
                if ($component !== 'all' && $component !== $service) {
                    continue;
                }

                // Redis pode devolver números como strings; rejeitar valores inválidos.
                $lastSeen = filter_var(Cache::get(config("watchdog.health.{$service}_key")), FILTER_VALIDATE_INT);
                $age = $lastSeen !== false ? now()->timestamp - $lastSeen : null;

                if ($age === null || $age < 0 || $age > config('watchdog.health.max_age_seconds')) {
                    $this->error("{$service}: sem atividade recente; confira os logs.");
                    $healthy = false;

                    continue;
                }

                $this->info("{$service}: ativo há {$age}s.");
            }

            return $healthy ? self::SUCCESS : self::FAILURE;
        } catch (Throwable $exception) {
            // Não imprimir mensagens de conexão que possam conter credenciais.
            $this->error('Falha de infraestrutura: '.$exception::class);

            return self::FAILURE;
        }
    }
}
