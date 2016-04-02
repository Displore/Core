<?php

namespace Displore\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Notifier extends Facade
{
    /**
     * Get the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notifier';
    }
}
