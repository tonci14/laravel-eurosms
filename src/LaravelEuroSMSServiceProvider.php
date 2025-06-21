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
            // Registrácia Artisan príkazov
            $this->commands([
                \Tonci14\LaravelEuroSMS\Console\Commands\SendTestSmsCommand::class,
            ]);

            // Registrácia publikovania configu
            $this->publishes([
                __DIR__ . '/../config/eurosms.php' => config_path('eurosms.php'),
            ], 'eurosms-config');

            // Registrácia publikovania migrácií
            $this->publishes([
                __DIR__ . '/../database/migrations/2024_01_01_000000_create_euro_sms_queue_table.php' =>
                    database_path('migrations/' . date('Y_m_d_His') . '_create_euro_sms_queue_table.php'),
            ], 'eurosms-migrations');
        }
    }
}
