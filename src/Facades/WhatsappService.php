<?php

namespace DavidArl\WaFiture\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DavidArl\WaFiture\WhatsappService
 *
 * @method static \DavidArl\WaFiture\WhatsappService device(Device $device)
 */
class WhatsappService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DavidArl\WaFiture\WhatsappService::class;
    }
}
