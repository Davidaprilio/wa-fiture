<?php

namespace Quods\Whatsapp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaServer extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at'];

    protected $hidden = ['api_key'];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function scopeEnable()
    {
        return $this->where('status', 'enable');
    }

    public function scopeDisable()
    {
        return $this->where('status', 'disable');
    }

    /**
     * Filter server have free slot
     */
    public function scopeAvailable()
    {
        return $this->withCount('devices')->having('devices_count', '>', 'max_devices')->enable();
    }

    public function makeDisable()
    {
        return $this->update(['status' => 'disable']);
    }

    public function makeEnable()
    {
        return $this->update(['status' => 'enable']);
    }
}
