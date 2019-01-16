<?php

namespace JanisKelemen\Setting\Providers;

use Illuminate\Support\ServiceProvider;
use JanisKelemen\Setting\EloquentStorage;
use JanisKelemen\Setting\Contracts\SettingStorageContract;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations')
        ], 'setting');

        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../../config/setting.php' => config_path('setting.php'),
        ], 'setting');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/setting.php',
        'setting'
        );
        // Register the service the package provides.
        $this->app->singleton('setting', function ($app) {
            return new Setting;
        });
        $this->app->bind(SettingStorageContract::class, EloquentStorage::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['setting'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {

        // Registering package commands.
        // $this->commands([]);
    }
}
