<?php

namespace Quods\Whatsapp;

use Quods\Whatsapp\Facades\WhatsappService;
use Quods\Whatsapp\Models\WaDevice;
use Closure;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class Whatsapp
{
    protected Copywriting $_copywriting;

    protected $text_message = '';

    protected $message_type = 'text';

    protected $payload = [];

    protected $button = [];

    protected $data = [];

    protected $title = null;

    protected $file_url = null;

    protected $save_result = false;

    protected $file_type = null;

    protected WaDevice $device;

    protected $phones = [];

    public function __construct(WaDevice $waDevice)
    {
        $this->device = $waDevice;
        $this->_copywriting = Copywriting::init()->withTimeData();
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
        if ($prefix) {
            $this->_copywriting->setVarPrefix($prefix);
        }
        $this->_copywriting->text($text);
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

        if ($this->message_type == 'Button') {
            $this->payload = array_merge($this->payload, [
                'message' => $this->text_message,
                'type' => $this->message_type,
            ]);
            $this->payload['payload'] = $this->button;

        } else if ($this->message_type == 'List') {
            $this->payload = [
                'message' => $this->text_message,
                'type' => $this->message_type,
                'payload' => $this->payload,
            ];

            $this->payload['payload']['text'] = $this->payload['message'];
            unset($this->payload['message']);
        }

        if ($this->file_url) {
            $this->payload['file_url'] = $this->file_url;
        }

        foreach ($phones as $phoneAndJID) {
            $this->payload['phone'] = $phoneAndJID;
            // reverse array
            $this->payload = array_reverse($this->payload);
            $res = WhatsappService::device($this->device)->send($this->payload);

            $report->push([
                'to' => $phoneAndJID,
                'status' => $res->status,
                'message' => $res->message,
                'data' => $res,
            ]);
        }

        if ($this->save_result) {
            $this->saveResult($report);
        }

        if ($phone == null) {
            return $report;
        }

        if ($report->count() > 1) {
            return $report;
        }

        return (object) $report->first();
    }

    public function save(string $title, ?string $table_name = null): self
    {
        $this->save_result = $table_name ?? true;
        $this->title = $title;
        return $this;
    }

    private function saveResult($result_data)
    {
        $data_insert = [];
        $now = now();

        foreach ($result_data as $data) {
            $data_insert[] = [
                'judul' => $this->title,
                'message' => $this->text_message,
                'type' => $this->message_type,
                'phone' => $data['to'],
                'pause' => 0,
                'proses_code' => str()->random(10),
                'status' => $data['message'] === 'Terkirim' ? 2 : 3,
                'report' => $data['message'],
                'messageid' => $data['message'] === 'Terkirim' ? $data['data']->data->messageid : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // return Antrian::zu($this->device->user_id)->insert($data_insert);
    }

    // ------------  Fiture  Whatsapp  ------------ //

    public function button(string $text, $reply = null, ?string $id = null): Whatsapp
    {
        $this->message_type = "Button";
        $countBtn = count($this->button);
        if ($countBtn > 3) {
            throw new \Exception('Error: maaf untuk sekarang button hanya bisa digunakan 3 kali');
        }
        if ($id === null) {
            $id = "btn" . ($countBtn + 1);
        }

        if ($reply) {
            if(gettype($reply) === 'array'){
                $reply = implode("\n", $reply);
            }
            $reply = Copywriting::init()->withTimeData()->data($this->data)->text($reply)->make()->get();
            $id = "{$id}<|>{$reply}";
        }

        $this->button[] = [
            'id' => $id,
            'text' => $text,
        ];

        return $this;
    }

    public function button_list(string $title, string $button = 'Pilih', $footer = null)
    {
        if ($this->message_type == 'Button') {
            throw new \Exception("Tidak bisa beralih dari Button ke List (kamu sudah menggunakab button)", 1);
        }

        $this->message_type = "List";       
        $this->payload = [
            'title' => $title,
            'footer' => $footer,
            'buttonText' => $button,
            'sections' => []
        ];
        return $this;
    }

    public function list(string $title, Closure $callback)
    {
        if ($this->message_type != "List") {
            throw new \Exception('Kamu harus memanggil method button_list() terlebih dahulu');
        }
        $items = new ListItem();
        $callback($items);

        $this->payload['sections'][] = [
            'title' => $title,
            'rows' => $items->getRows(),
        ];
    }

    public function file(string $url)
    {
        $type = $this->check_file_type($url);
        if ($this->message_type === 'Button' && $type !== 'Image') {
            throw new \Exception("Error: maaf Whatsapp belum support mengirim {$type} dengan Button (hanya support Image dengan Button)");
        }
        $this->file_url = $url;
    }

    /**
     * Set sender device
     *
     * @param  WaDevice|int  $device WaDevice model or device id for sending message
     * @return Whatsapp
     */
    public static function device($device)
    {
        if (!($device instanceof WaDevice)) {
            $device_id = $device;
            $device = WaDevice::find($device_id);
            if (is_null($device)) {
                throw new NotFoundResourceException("WaDevice with id ({$device_id}) Not Found");
            }
        }

        return new self($device);
    }

    private function check_file_type($url)
    {
        $file_type = pathinfo($url, PATHINFO_EXTENSION);
        $ext_image = ['jpg', 'jpeg', 'png', 'gif'];
        $ext_video = ['mp4', 'mkv', 'avi', '3gp'];
        $ext_audio = ['mp3', 'wav', 'ogg'];

        if (in_array($file_type, $ext_image)) {
            $this->file_type = 'Image';
        } elseif (in_array($file_type, $ext_video)) {
            $this->file_type = 'Video';
        } elseif (in_array($file_type, $ext_audio)) {
            $this->file_type = 'Audio';
        } else {
            $this->file_type = 'File';
        }

        return $this->file_type;
    }
}
