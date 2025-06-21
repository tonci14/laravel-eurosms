<?php

namespace Tonci14\LaravelEuroSMS;

use Illuminate\Support\ServiceProvider;
use Tonci14\LaravelEuroSMS\Services\EuroSmsService;

class LaravelEuroSMSServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Konfigurácia
        $this->mergeConfigFrom(__DIR__ . '/../config/eurosms.php', 'eurosms');

        // Registrácia singletonu
        $this->app->singleton('eurosms', function ($app) {
            return new EuroSmsService(config('eurosms'));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Tonci14\LaravelEuroSMS\Console\Commands\SendTestSmsCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/eurosms.php' => config_path('eurosms.php'),
            ], 'eurosms-config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }
}
