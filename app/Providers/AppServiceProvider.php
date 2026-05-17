<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Fix migration table configuration issue
        $this->app->bind('migration.repository', function ($app) {
            $table = $app['config']['database.migrations.table'];
            return new \Illuminate\Database\Migrations\DatabaseMigrationRepository(
                $app['db'],
                $table
            );
        });
    }
}
