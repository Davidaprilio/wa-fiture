<?php

namespace Quods\Whatsapp;

use Carbon\Carbon;
use Quods\Whatsapp\Models\WaDevice;
use Quods\Whatsapp\Models\WaMessage;
use Illuminate\Support\Str;

/**
 * Easy And Fast Saving Message to table
 */
class MessageBuilder
{
    private string $title;

    private ?WaDevice $device;

    private ?int $user_id;

    private ?string $copywriting;

    private $limit_quota = INF;

    private bool $stop_after_limit = true;

    private ?int $message_today;

    private ?int $remaining_limit;

    private string $process_id;

    private bool $auto_create_table = false;

    private int $priority = 10;

    private string $type = 'text';

    private array $data = [];

    private array $button = [];

    private array $file = [
        'file' => null,
        'file_name' => null,
    ];

    private $now;

    private array $pause = [
        'min' => 1,
        'max' => 2,
    ];

    private array $results = [];

    public function __construct($device, int $length_process_id = 20)
    {
        if ($device instanceof WaDevice) {
            $this->device = $device;
        } elseif (is_numeric($device)) {
            $this->device = WaDevice::find($device);
        } else {
            throw new \Exception('WaDevice not found, please check your device id or instance of WaDevice Model');
        }

        $this->user_id = $this->device->user_id;
        if (is_numeric($this->user_id)) {
            $this->user_id = (int) $this->user_id;
        } else {
            throw new \Exception("User id not found, please check user_id on your device with id:{$this->device->id}");
        }

        $this->now = Carbon::now();
        $this->process_id = Str::random($length_process_id);
    }

    public static function device($device): self
    {
        return new self($device);
    }

    /**
     * Tetapkan Limitasi Pesan Harian
     *
     * @param  int  $limit Jumlah Limit Pesan
     * @param  bool  $stop_after_limit Jika true, maka akan berhenti setelah limit tercapai pesan selanjutnya tidak akan dibuat
     * @param  array  $type_state kondisi status yang akan dihitung sebagai pesan harian
     * @return self
     */
    public function limitQuota(int $limit, bool $stop_after_limit = true, array $type_state = ['sent', 'limit', 'not-wa', 'read', 'creating', 'sending']): self
    {
        $this->limit_quota = $limit;
        $this->message_today = WaMessage::zu($this->user_id)
            ->today()
            ->whereIn('status', $type_state)
            ->count() ?? 0;
        $this->remaining_limit = max(0, $this->limit_quota - $this->message_today);
        $this->stop_after_limit = $stop_after_limit;

        return $this;
    }

    /**
     * Tetapkan judul pesan
     *
     * @param  string  $title Judul Pesan
     * @return self
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Tetapkan Prioritas pengiriman pesan
     *
     * @param  int  $priority Semakin kecil semakin diprioritaskan
     * @return self
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Tetapkan Tipe Pesan
     *
     * @param  string  $type Tipe Pesan
     * @return self
     */
    public function setPause(int $min, int $max): self
    {
        $this->pause = ['min' => $min, 'max' => $max];

        return $this;
    }

    private function randPause(): int
    {
        return random_int($this->pause['min'], $this->pause['max']);
    }

    /**
     * Copywriting pesan yang akan dikirim Bisa menggunakan format variable
     *
     * @param  string  $copywriting Text Copywriting
     */
    public function copywriting(string $copywriting): self
    {
        $this->copywriting = $copywriting;

        return $this;
    }

    /**
     * Tambah Pesan Dengan Button otomatis akan mengubah tipe pesan menjadi button
     *
     * @param  string  $text Text pada Button
     * @param  string  $replay Text Pesan yang akan dikirim jika button di klik
     */
    public function button(string $text, string $replay)
    {
        $this->type = 'button';
        $count = count($this->button) / 2 + 1;
        if ($count > 3) {
            throw new \Exception('Error: maaf untuk sekarang button hanya bisa digunakan 3 kali');
        }
        $this->button["button{$count}"] = $text;
        $this->button["action{$count}"] = $replay;

        return $this;
    }

    /**
     * Sematkan File pada Pesan dengan mengisi url file (hanya Image jpg/png/gif)
     *
     * @param  string  $url Url File
     * @param  string  $file_name Nama File jika tidak diisi maka akan menggunakan nama file asli dari url
     */
    public function file(string $url_file, string $file_name = null): self
    {
        $this->file['file'] = $url_file;
        $this->file['file_name'] = $file_name ?? basename($url_file);

        return $this;
    }

    private function generateTextButton(array $button, array $data): array
    {
        $button_ready = [];
        foreach ($button as $column => $value) {
            $button_ready[$column] = Copywriting::init()->text($value)->data($data)->make()->get();
        }

        return $button_ready;
    }

    /**
     * Membuat data pesan dari config yang diberikan
     *
     * @param  string  $phone Nomor Telepon yang akan dikirim pesan
     * @param  array  $data Data yang akan digunakan untuk membuat Copywriting pesan
     */
    public function add(string $phone, array $data)
    {
        $state = $this->getStateLimitOrCreating();
        if ($state === 'limit' && $this->stop_after_limit) {
            // Reject jika sudah melebihi limit
            $this->data = [];

            return $this;
        }

        $txt = Copywriting::init()->text($this->copywriting)->data($data)->make()->get();
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $buttons = [];
        if ($this->type === 'button') {
            $buttons = $this->generateTextButton($this->button, $data);
        }
        $this->data = [
            'user_id' => $this->user_id,
            'process_id' => $this->process_id,
            'device_id' => $this->device->id,
            'judul' => $this->title,
            'type_message' => $this->type,
            'phone' => $phone,
            'status' => $state,
            'text' => $txt,
            'priority' => $this->priority,
            'pause' => $this->randPause(),
            'payload' => '[]',
            ...$this->file,
            ...$buttons,
        ];
        $this->results[] = [
            ...$this->data,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ];

        return $this;
    }

    public function getStateLimitOrCreating()
    {
        if ($this->limit_quota !== INF) {
            if ($this->remaining_limit <= 0) {
                return 'limit';
            } else {
                $this->remaining_limit--;
            }
        }

        return 'creating';
    }

    public function setAutoCreateTable(bool $auto_create_table): self
    {
        $this->auto_create_table = $auto_create_table;

        return $this;
    }

    public function create(string $phone, array $data): self
    {
        $this->add($phone, $data);
        if (count($this->data) > 0) {
            WaMessage::zu($this->user_id, $this->auto_create_table)->create($this->data);
        }

        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function save()
    {
        return WaMessage::zu($this->user_id, $this->auto_create_table)->insert($this->results);
    }
}
