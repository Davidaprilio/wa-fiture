<?php

namespace DavidArl\WaFiture;

use Carbon\Carbon;
use DavidArl\WaFiture\Facades\WhatsappService;
use DavidArl\WaFiture\Models\Device;

class Whatsapp
{

    protected $copywriting = '';
    protected $text_message = '';
    protected $message_type = 'text';
    protected $payload = [];
    protected $data = [];
    protected $prefix_variable = ':var';
    protected Device $device;
    protected $phones = [];

    public function __construct(Device $device)
    {
        $this->device = $device;

        $dayTime = self::currentDayTime();
        $now = Carbon::now();
        $this->data = [
            '_dayName' => strtolower($now->dayName),
            '_monthName' => strtolower($now->monthName),
            '_DayName' => $now->dayName,
            '_MonthName' => $now->monthName,
            '_hours' => $now->format('H:i'),
            '_day'  => $now->day,
            '_month' => $now->month,
            '_year' => $now->year,
            '_time' => $dayTime,
            '_Time' => ucfirst($dayTime),
        ];
    }

    public function data(array $data)
    {
        $this->data = array_merge($this->data, $data);
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
     * @param String|Array $text Message copywriting include variable and spintext
     * @param String $prefix Prefix for variable format, Example :var use :_dayName | [var] use [_dayName]
     */
    public function copywriting($text, string $prefix = ':var'): Whatsapp
    {
        if (is_array($text)) {
            $text = self::printText($text);
        }
        $this->copywriting = $text;
        $this->prefix_variable = $prefix;

        $this->text_message = $this->copywriting;
        foreach ($this->data as $key => $value) {
            $key = str_replace('var', $key, $prefix);
            $this->text_message = str_replace($key, $value, $this->text_message);
        }
        $this->text_message = self::spintext($this->text_message);

        return $this;
    }

    /**
     * Get Copywriting
     */
    public function getCopywriting()
    {
        return $this->copywriting;
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
     * @param string|array $phone phone or jid format ['xxxx', 'xxxx'] | 'xxxx' | 'xxxx,xxxx'
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
                'to' => $phone,
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
     * @param Device|Int $device Device model or device id for sending message
     * 
     * @return Whatsapp
     */
    public static function device($device)
    {
        if (!($device instanceof Device)) {
            $device = Device::find($device);
        }
        return new self($device);
    }

    public static function spintext($text)
    {
        return preg_replace_callback("/{(.*?)}/", function ($match) {
            $words = explode("|", $match[1]);
            return $words[array_rand($words)];
        }, $text);
    }

    public static function currentDayTime(): string
    {
        $textTime = [
            'morning' => [
                'id' => 'pagi',
                'en' => 'morning',
            ],
            'afternoon' => [
                'id' => 'siang',
                'en' => 'afternoon',
            ],
            'evening' => [
                'id' => 'sore',
                'en' => 'evening',
            ],
            'night' => [
                'id' => 'malam',
                'en' => 'night',
            ],
        ];

        $local = config('app.locale') ?? 'id';

        $hour = Carbon::now()->hour;
        if ($hour >= 3 && $hour <= 10) {
            return $textTime['morning'][$local];
        } else if ($hour >= 11 && $hour <= 15) {
            return $textTime['afternoon'][$local];
        } else if ($hour >= 15 && $hour <= 20) {
            return $textTime['evening'][$local];
        } else {
            return $textTime['night'][$local];
        }
    }


    public static function printText(array $string): string
    {
        $text = "";
        $first = true;
        foreach ($string as $s) {
            ($first) ?
                $first = false :
                $text .= "\n";

            $text .= "{$s}";
        }
        return $text;
    }
}
