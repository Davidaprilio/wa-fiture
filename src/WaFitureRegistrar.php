<?php

namespace Quods\Whatsapp;

class WaFitureRegistrar
{
    protected $deviceModelClass;

    protected $serverModelClass;

    protected $contactModelClass;

    protected $messageModelClass;

    protected $notificationModelClass;

    protected $roleModelClass;

    protected $permissionModelClass;

    public function __construct()
    {
        $this->deviceModelClass = config('wa-fiture.models.device');
        $this->serverModelClass = config('wa-fiture.models.server');
        $this->contactModelClass = config('wa-fiture.models.contact');
        $this->messageModelClass = config('wa-fiture.models.message');
        $this->notificationModelClass = config('wa-fiture.models.notification');
    }

    public function getDeviceClass()
    {
        return $this->deviceModelClass;
    }

    public function getServerClass()
    {
        return $this->serverModelClass;
    }

    public function getContactClass()
    {
        return $this->contactModelClass;
    }

    public function getMessageClass()
    {
        return $this->messageModelClass;
    }

    public function getNotificationClass()
    {
        return $this->notificationModelClass;
    }
}
