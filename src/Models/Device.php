<?php

namespace DavidArl\WaFiture\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Device extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at'];

    public function wa_server()
    {
        return $this->belongsTo(WaServer::class);
    }

    public function scopeUserId($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeOnServer($query, $server_id)
    {
        return $query->where('wa_server_id', $server_id);
    }

    public function generateApiKey()
    {
        $this->api_key = str()->random(60);
        return $this->save();
    }

    /**
     * @param String|Int|WaServer $server bisa berupa id, nama, server atau model dari WaServer
     */
    public function changeServer($server)
    {
        if (!($server instanceof WaServer)) { // if not instance of WaServer
            $server = WaServer::where('id', $server)->orWhere('name', $server)->first();
            if ($server == null) {
                Log::debug("Can't change server to $server, server not found");
                return false;
            }
        }
        $this->wa_server_id = $server->id;
        return $this->save();
    }
}
