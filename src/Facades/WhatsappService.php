<?php

namespace Quods\Whatsapp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Quods\Whatsapp\WhatsappService
 *
 * @method static \Quods\Whatsapp\WhatsappService device(Device $device)
 */
class WhatsappService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Quods\Whatsapp\WhatsappService::class;
    }
}
