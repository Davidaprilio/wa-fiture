<?php

namespace DavidArl\WaFiture\Models;

use DavidArl\WaFiture\Whatsapp;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at'];

    protected $casts = [
        'enable' => 'boolean',
        'variable' => AsArrayObject::class,
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enable', 1);
    }

    public function scopeDisabled($query)
    {
        return $query->where('enable', 0);
    }

    public function scopeName($query, $name)
    {
        return $query->where('name', $name);
    }

    public function makeDisable()
    {
        return $this->update(['enable' => 0]);
    }

    public function makeEnable()
    {
        return $this->update(['enable' => 1]);
    }

    public function send(array $phones, array $data)
    {
        return Whatsapp::device($this->device)
            ->data($data)
            ->copywriting($this->copywriting)
            ->to($phones)
            ->send();
    }
}
