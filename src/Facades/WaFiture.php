<?php

namespace DavidArl\WaFiture\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @author David Arl
 *
 * @see \DavidArl\WaFiture\WaFiture
 *
 * @property Device $device
 *
 * @method static ?bool instanceofCollection($class, $collectionOrClass) Check instanceof Class or Collection of Class, 
 * return null if collection is empty
 * @method static \DavidArl\WaFiture\WaFiture device(Device $device)
 * @method public object getQRandStatus()
 */
class WaFiture extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DavidArl\WaFiture\WaFiture::class;
    }
}
