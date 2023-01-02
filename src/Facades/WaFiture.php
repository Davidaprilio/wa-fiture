<?php

namespace Quods\Whatsapp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @author David Arl
 *
 * @see \Quods\Whatsapp\WaFiture
 *
 * @property Device $device
 *
 * @method static ?bool instanceofCollection($class, $collectionOrClass) Check instanceof Class or Collection of Class, 
 * return null if collection is empty
 * @method static \Quods\Whatsapp\WaFiture device(Device $device)
 * @method public object getQRandStatus()
 */
class WaFiture extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Quods\Whatsapp\WaFiture::class;
    }
}
