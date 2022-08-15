<?php

namespace DavidArl\WaFiture\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DavidArl\WaFiture\WaFiture
 */
class WaFiture extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DavidArl\WaFiture\WaFiture::class;
    }
}
