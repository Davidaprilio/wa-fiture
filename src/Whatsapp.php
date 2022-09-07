<?php

namespace DavidArl\WaFiture;

use Carbon\Carbon;
use DavidArl\WaFiture\Facades\WhatsappService;
use DavidArl\WaFiture\Models\Device;

class Whatsapp
{
    protected Copywriting $_copywriting;

    protected $text_message = '';

    protected $message_type = 'text';

    protected $payload = [];

    protected $data = [];

    protected Device $device;

    protected $phones = [];

    public function __construct(Device $device)
    {
        $this->device = $device;
        $this->_copywriting = Copywriting::text('')->withTimeData();
    }

    public function data(array $data)
    {
        $this->data = $data;
        $this->_copywriting->data($data);
        return $this;
    }

    /**
     * Insert Your Message Copywriting here
     *
     * Default variable :
     *  _hours = "16:44"
     *  _day = 29  |   _month = 8  |   _year = 2022  |
     *  _dayName = "senin"  |   _monthName = "agustus"  |
     *  _DayName = "Senin"  |   _MonthName = "Agustus"  |
     *  _time = "sore"  |   _Time = "Sore"
     *
     * @param  string|array  $text Message copywriting include variable and spintext
     * @param  string  $prefix Prefix for variable format,  Default ':var'   Example :var use :_dayName | [var] use [_dayName]
     */
    public function copywriting($text, string $prefix = null): Whatsapp
    {
        $this->_copywriting->text($text);
        if ($prefix) {
            $this->_copywriting->setPrefix($prefix);
        }
        $this->_copywriting->make();
        $this->text_message = $this->_copywriting->get();
        return $this;
    }

    /**
     * Get Copywriting
     */
    public function getCopywriting()
    {
        return $this->_copywriting->getCopywriting();
    }

    // /**
    //  * Sending to given contact Model
    //  *
    //  * @param string|array $contact
    //  */
    // public function contact(Contact $contact)
    // {
    //     return $this->text_message;
    // }

    /**
     * Sending to given phone or jid
     *
     * @param  string|array  $phone phone or jid format ['xxxx', 'xxxx'] | 'xxxx' | 'xxxx,xxxx'
     */
    public function to($phone): Whatsapp
    {
        if (!is_array($phone)) {
            $phone = explode(',', $phone);
        }
        $this->phones = array_merge($this->phones, $phone);
        return $this;
    }

    /**
     * Sending message
     *
     * @param $phone If given phone, message will be sent to this phone only
     */
    public function send(string $phone = null)
    {
        if ($phone == null) {
            if (count($this->phones) > 0) {
                $phones = $this->phones;
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No phone number given',
                ];
            }
        } else {
            $phones = [$phone];
        }

        $report = collect();
        foreach ($phones as $phoneAndJID) {
            $res = WhatsappService::device($this->device)->send([
                'phone' => $phoneAndJID,
                'message' => $this->text_message,
                'type' => $this->message_type,
            ]);

            $report->push([
                'to' => $phoneAndJID,
                'status' => $res->status,
                'message' => $res->message,
                'data' => $res,
            ]);
        }

        if ($phone == null) {
            return $report;
        }

        return (object) $report->first();
    }

    public function save()
    {
        $data = [
            'device_id' => $this->device->id,
            'message' => $this->text_message,
            'contact' => $this->contact,
        ];

        return $data;
    }

    public function button($text, $id): Whatsapp
    {
        $this->message_type = 'Button';
        $this->payload['buttons'] = (object) [
            'text' => $text,
            'id' => $id,
        ];
        return $this;
    }

    /**
     * Set sender device
     *
     * @param  Device|int  $device Device model or device id for sending message
     * @return Whatsapp
     */
    public static function device($device)
    {
        if (!($device instanceof Device)) {
            $device = Device::find($device);
        }
        return new self($device);
    }
}
