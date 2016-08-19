<?php

namespace Displore\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        // The general configuration.
        $this->publishes([
            __DIR__.'/../config/core.php' => config_path('displore/core.php'),
        ], 'displore.core.config');

        $this->mergeConfigFrom(__DIR__.'/../config/core.php', 'displore.core');
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        // The install commands are not necessary on production servers.
        if ($this->app->environment('local')) {
            $this->app->register('Displore\Core\Installer\InstallerServiceProvider');
        }

        $this->app->singleton('logbook', function () {
            return $this->app->make('Displore\Core\Logging\Logbook');
        });
    }
}
