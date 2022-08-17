<?php

namespace DavidArl\WaFiture\Models;

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

    public function scopeEnabled()
    {
        return $this->where('status', 'enable');
    }

    public function scopeDisabled()
    {
        return $this->where('status', 'disable');
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
