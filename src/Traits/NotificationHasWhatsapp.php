<?php

namespace DavidArl\WaFiture\Traits;

use DavidArl\WaFiture\Models\Device;
use DavidArl\WaFiture\Whatsapp;

/**
 * Use this trait in your model Notification to scoping and Easy sending Notification
 * Note: please insert $casts with :
 * 'enable' => 'boolean'
 * 'variable' => AsArrayObject::class,
 * 
 * -- Lak Ngoding Ojo Mumet-Mumet Lemesne Wae
 */
trait NotificationHasWhatsapp
{
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
