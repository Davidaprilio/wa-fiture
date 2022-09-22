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
        'server' => DavidArl\WaFiture\Models\WaServer::class,
        'device' => DavidArl\WaFiture\Models\Device::class,
        'contact' => DavidArl\WaFiture\Models\Contact::class,
        'message' => DavidArl\WaFiture\Models\Message::class,
        'notification' => DavidArl\WaFiture\Models\Notification::class,
    ],

];
