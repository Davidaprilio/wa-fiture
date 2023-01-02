<?php

namespace Quods\Whatsapp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeviceFactory extends Factory
{
    protected $model = YourModel::class;

    public function definition()
    {
        return [
            'wa_server_id' => 1,
            'user_id' => 1,
            'mode' => 'md',
            'device_key' => Str::random(10),
        ];
    }
}
