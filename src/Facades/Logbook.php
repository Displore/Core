<?php

namespace Displore\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Logbook extends Facade
{
    /**
     * Get the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'logbook';
    }
}
