<?php

namespace Displore\Core\Installer;

use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'Displore\Core\Installer\InstallCommand',
    ];

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
