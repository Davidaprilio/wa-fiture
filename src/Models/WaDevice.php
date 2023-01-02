<?php

namespace Quods\Whatsapp\Models;

use Quods\Whatsapp\Traits\DeviceHasWhatsapp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaDevice extends Model
{
    use HasFactory,
        DeviceHasWhatsapp;

    protected $guarded = ['id', 'created_at'];
}
