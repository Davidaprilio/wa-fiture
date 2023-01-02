<?php

namespace Quods\Whatsapp\Models;

use Quods\Whatsapp\Traits\NotificationHasWhatsapp;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaNotification extends Model
{
    use HasFactory,
        NotificationHasWhatsapp;

    protected $guarded = ['id', 'created_at'];

    protected $casts = [
        'enable' => 'boolean',
        'variable' => AsCollection::class,
    ];
}
