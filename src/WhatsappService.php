<?php

namespace DavidArl\WaFiture;

use DavidArl\WaFiture\Models\Device;

class WhatsappService
{
    protected Device $device;
    protected $data = [];

    public static function device(Device $device)
    {
        $wa = new self();
        $wa->device = $device;
        return $wa;
    }

    /**
     * @param Int|String $device_id Device untuk mengirim pesan
     */
    public static function token(int $device_id)
    {
        $device = Device::find($device_id);
        $wa = new self();
        $wa->device = $device;
        return $wa;
    }

    public function cpu()
    {
        $device = Device::first();
        return self::device($device)->curl('cpu', 'GET');
    }

    public function list()
    {
        $device = Device::first();
        return self::device($device)->curl("api/devices", "GET");
    }

    public static function servers()
    {
        $device = Device::first();
        return self::device($device)->curl("api/servers", "GET");
    }

    public function data(array $data)
    {
        $this->data = $data;
        return $this;
    }


    public function start(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/start", $data);
    }

    public function qrcode(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/qrcode", "GET");
    }

    public function send(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/send");
    }

    public function queue(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/antrian");
    }

    public function cek_antrian(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/cek-antrian");
    }

    public function delete(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/delete-antrian");
    }

    public function logout(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/logout", "POST");
    }

    public function close(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/close", "POST");
    }

    public function listgroup(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/group-list-with-contact");
    }

    public function restart(array $data = null)
    {
        if ($data) $this->data = $data;
        return $this->curl("api/restart", "POST");
    }

    private function curl($url, $method = "POST"): object
    {
        $device = $this->device;
        $device->load('wa_server');
        $server = $device->wa_server;
        $this->data['token'] = $device->id;
        // return $device;
        if ($device) {
            $url = $server->ip . ":" . $server->port . "/" . $url;
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => json_encode($this->data),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "apikey: " . $server->api_key

                ],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return (object) [
                    'status' => false,
                    'message' => "Server offline",
                    "error" => $err,
                    'data' => $this->data
                ];
            } else {
                if (gettype($response) == "string") {
                    $response = json_decode($response);
                }
                return $response;
            }
        } else {
            return (object) [
                'status' => false,
                'message' => "Device not found",
                'data' => $this->data
            ];
        }
    }
}
