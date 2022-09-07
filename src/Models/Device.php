<?php

namespace DavidArl\WaFiture\Models;

use DavidArl\WaFiture\Traits\DeviceHasWhatsapp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory,
        DeviceHasWhatsapp;

    protected $guarded = ['id', 'created_at'];
}
