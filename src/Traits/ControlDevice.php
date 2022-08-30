<?php

namespace DavidArl\WaFiture\Traits;

use DavidArl\WaFiture\Facades\WhatsappService;
use DavidArl\WaFiture\Models\Device;
use Exception;

/**
 * @author David Arl <
 */
trait ControlDevice
{
    protected ?Device $device = null;
    protected $mode = ['md', 'std'];
    protected $status = [
        'auth' => 'AUTHENTICATED',
        'not_auth' => 'NOT AUTHENTICATED',
    ];

    /**
     * @param Device|Int $device device model or id
     */
    public function device($device)
    {
        if (!($device instanceof Device)) {
            $device = Device::find($device);
        }
        $this->device = $device;
        return $this;
    }

    /**
     * Handle Start device
     */
    public function start($mode = null)
    {
        $this->validateDevice();
        if ($mode !== null) {
            $this->hasMode($mode);
            $this->device->update([
                'mode' => $mode,
            ]);
            $this->device->refresh();
        }

        return WhatsappService::device($this->device)
            ->data(['mode' => $this->device->mode])
            ->start();
    }


    /**
     * Logout Device
     */
    public function logout()
    {
        $this->validateDevice();
        $result = WhatsappService::device($this->device)->logout();
        $this->device->update([
            'status' => $this->status['not_auth'],
            'phone' => null,
            'photo' => null
        ]);
        return $result;
    }


    /**
     * Restart Device (logout and Start)
     */
    public function restart()
    {
        $this->logout();
        $res = $this->start();
        return $res;
    }



    /**
     * Get Device Status and Get QRcode
     * automatic save data to device Model
     */
    public function getQRandStatus()
    {
        $this->validateDevice();
        $device = $this->device;
        $result = WhatsappService::device($device)->qrcode();
        $status = null;
        if ($result->message == $this->status['auth']) {
            if ($device) {
                $phone = explode(':', $result->data->id)[0] ?? null;
                $device->update([
                    'photo' => $result->pic ?? null,
                    'status' => $this->status['auth'],
                    'phone' => $phone
                ]);
            }
        } else if ($result->message == 'token tidak tersedia') {
            $status = "Device+Offline";
        } else {
            $device->update([
                'mode' => $result->mode ?? $device->mode,
                'status' => $this->status['not_auth'],
                'photo' => null,
                'phone' => null
            ]);
        }
        $status = $status ?? "Loading...";
        $result->image = $result->pic ?? $result->qrcode ?? "https://via.placeholder.com/500?text={$status}";
        $result->phone = $phone ?? null;
        $result->server = $device->server_id;
        $result->mode =  $result->mode ?? $device->mode;
        return $result;
    }


    protected function hasMode($mode): string
    {
        if (!in_array($mode, $this->mode)) {
            throw new Exception("Device mode '{$mode}' not found enums: " . implode(', ', $this->mode));
        }
        return $mode;
    }


    protected function validateDevice(): void
    {
        if ($this->device == null) {
            throw new Exception('Device not set');
        }
        if (!($this->device instanceof Device)) {
            throw new Exception('Device not instance of Device Model');
        }
    }
}
