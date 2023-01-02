<?php

namespace Quods\Whatsapp\Traits;

use Quods\Whatsapp\Facades\WhatsappService;
use Quods\Whatsapp\Models\WaDevice;
use Exception;

/**
 * @author David Arl <
 */
trait ControlDevice
{
    protected ?WaDevice $device = null;

    protected $mode = ['md', 'std'];

    protected $statusAuth = [
        'auth' => 'AUTHENTICATED',
        'not_auth' => 'NOT AUTHENTICATED',
    ];

    /**
     * @param WaDevice|int  $device device model or id
     */
    public function device($device)
    {
        if (!($device instanceof WaDevice)) {
            $device = WaDevice::find($device);
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
            'status' => $this->statusAuth['not_auth'],
            'phone' => null,
            'photo' => null,
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
                    'status' => $this->statusAuth['auth'],
                    'phone' => $phone,
                ]);
            }
        } elseif ($result->message == 'token tidak tersedia') {
            $status = 'Device+Offline';
        } else {
            $device->update([
                'mode' => $result->mode ?? $device->mode,
                'status' => $this->statusAuth['not_auth'],
                'photo' => null,
                'phone' => null,
            ]);
        }
        $status = $status ?? 'Loading...';
        $result->image = $result->pic ?? $result->qrcode ?? "https://via.placeholder.com/500?text={$status}";
        $result->phone = $phone ?? null;
        $result->server = $device->server_id;
        $result->mode = $result->mode ?? $device->mode;

        return $result;
    }

    protected function hasMode($mode): string
    {
        if (!in_array($mode, $this->mode)) {
            throw new Exception("WaDevice mode '{$mode}' not found enums: " . implode(', ', $this->mode));
        }

        return $mode;
    }

    protected function validateDevice(): void
    {
        if ($this->device == null) {
            throw new Exception('WaDevice not set');
        }
        if (!($this->device instanceof WaDevice)) {
            throw new Exception('WaDevice not instance of WaDevice Model');
        }
    }
}
