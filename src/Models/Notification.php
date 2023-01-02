<?php

namespace Quods\Whatsapp\Models;

use Quods\Whatsapp\Traits\NotificationHasWhatsapp;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory,
        NotificationHasWhatsapp;

    protected $guarded = ['id', 'created_at'];

    protected $casts = [
        'enable' => 'boolean',
        'variable' => AsArrayObject::class,
    ];
}
