<?php

// config for DavidArl/WaFiture
return [
    /**
     * If enabled, WaFiture check permission in every fiture
     * use permission from spatie/laravel-permission
     */
    'use-permissions' => true,

    /**
     * Default prefix for format variable copywriting
     * Example Fromat:
     *    :var  usage :_dayName
     *    [var] usage [_dayName]
     */
    'prefix-variable' => ':var',

    'models' => [
        'server' => Quods\Whatsapp\Models\WaServer::class,
        'device' => Quods\Whatsapp\Models\Device::class,
        'contact' => Quods\Whatsapp\Models\Contact::class,
        'message' => Quods\Whatsapp\Models\Message::class,
        'notification' => Quods\Whatsapp\Models\Notification::class,
    ],

];
