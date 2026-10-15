<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LogicException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('testing') && (
            config('database.default') !== 'mysql'
            || config('database.connections.mysql.host') !== 'mysql_test'
            || (int) config('database.connections.mysql.port') !== 3306
            || config('database.connections.mysql.database') !== 'crawler_token_test'
            || filled(config('database.connections.mysql.url'))
            || filled(config('database.connections.mysql.unix_socket'))
        )) {
            throw new LogicException('Testing exige mysql_test:3306/crawler_token_test, sem DB_URL ou socket alternativo.');
        }
    }
}
