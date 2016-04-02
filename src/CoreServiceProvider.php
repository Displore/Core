<?php

namespace Displore\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // The general configuration.
        $this->publishes([
            __DIR__.'/../config/core.php' => config_path('displore/core.php'),
        ], 'displore.core.config');
        $this->mergeConfigFrom(
            __DIR__.'/../config/core.php', 'displore.core'
        );

        // The basic menu implementation configuration.
        $this->publishes([
            __DIR__.'/../config/menu.php' => config_path('displore/menu.php'),
        ], 'displore.core.menu');
        $this->mergeConfigFrom(
            __DIR__.'/../config/menu.php', 'displore.menu'
        );

        // Migrations for tags.
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'displore.core.migrations');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // The install commands are not necessary on production servers.
        if ($this->app->environment('local')) {
            $this->app->register('Displore\Core\Installer\InstallerServiceProvider');
        }

        $this->app->singleton('notifier', function () {
            return $this->app->make('Displore\Core\Notifications\Notifier');
        });

        $this->app->singleton('tagger', function () {
            return $this->app->make('Displore\Core\Tags\Tagger');
        });

        $this->app->singleton('logbook', function () {
            return $this->app->make('Displore\Core\Logging\Logbook');
        });

        $this->app->bind('Displore\Core\Contracts\MenuBuilder', 'Displore\Core\Menu\Builder');
        $this->app->bind('menu', function () {
            return $this->app->make('Displore\Core\Contracts\MenuBuilder');
        });
    }
}
