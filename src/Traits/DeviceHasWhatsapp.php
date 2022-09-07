<?php

namespace DavidArl\WaFiture\Traits;

use App\Models\User;
use DavidArl\WaFiture\Models\WaServer;
use DavidArl\WaFiture\Whatsapp;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Use this trait in your model Device
 * to make amazing action with Whatsapp
 *
 * -- Hope this Trait helps you
 */
trait DeviceHasWhatsapp
{
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
        $this->device_key = str()->random(60);

        return $this->save();
    }

    /**
     * @param  string|int|WaServer  $server bisa berupa id, nama, server atau model dari WaServer
     */
    public function changeServer($server)
    {
        if (! ($server instanceof WaServer)) { // if not instance of WaServer
            $server = WaServer::where('id', $server)->orWhere('name', $server)->first();
            if ($server == null) {
                Log::debug("Server Not Found, Can't change server to $server");

                return false;
            }
        }
        $this->wa_server_id = $server->id;

        return $this->save();
    }

    /**
     * @param  string  $name nama device
     * @param  int|User  $user bisa berupa id_user atau model dari User
     * @param  string|int|WaServer  $server bisa berupa id, nama, server atau model dari WaServer
     */
    public static function new(string $name, $user, $server = null): self
    {
        if (! ($user instanceof User)) {
            $user = User::find($user);
            if ($user == null) {
                throw new Exception("User Not Found, Can't create device for user $user", 500);

                return false;
            }
        }

        if (! ($server instanceof WaServer)) { // if not instance of WaServer
            $server = WaServer::where('id', $server)->orWhere('name', $server)->first();
            if ($server == null) {
                throw new Exception("Server Not Found, Can't change server to $server", 500);

                return false;
            }
        } elseif ($server == null) {
            $servers = WaServer::available()->get();
            if ($servers->count()) {
                $server = $servers->first();
            } else {
                throw new Exception('Server sudah penuh (server is full)', 500);
            }
        }

        return self::create([
            'name' => $name,
            'user_id' => $user->id,
            'wa_server_id' => $server->id,
        ]);
    }

    /**
     * Shortcut to send Whatsapp directly from Device Model
     */
    public function whatsapp()
    {
        return Whatsapp::device($this);
    }
}
