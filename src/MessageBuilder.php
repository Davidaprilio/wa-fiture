<?php

namespace DavidArl\WaFiture;

use Carbon\Carbon;
use DavidArl\WaFiture\Models\Device;
use DavidArl\WaFiture\Models\Messages;
use Illuminate\Support\Str;

/**
 * Easy And Fast Saving Message to table
 */
class MessageBuilder
{
    private string $title;

    private ?Device $device;

    private ?int $user_id;

    private ?string $copywriting;

    private string $process_id;

    private bool $auto_create_table = false;

    private int $priority = 10;

    private string $type = 'text';

    private array $data = [];

    private $now;

    private array $pause = [
        'min' => 1,
        'max' => 2,
    ];

    private array $results = [];

    public function __construct($device, int $length_process_id = 20)
    {
        if ($device instanceof Device) {
            $this->device = $device;
        } elseif (is_numeric($device)) {
            $this->device = Device::find($device);
        } else {
            throw new \Exception('Device not found, please check your device id or instance of Device Model');
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

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function setPause(int $min, int $max): self
    {
        $this->pause = ['min' => $min, 'max' => $max];

        return $this;
    }

    private function randPause(): int
    {
        return rand($this->pause['min'], $this->pause['max']);
    }

    public function copywriting(string $copywriting): self
    {
        $this->copywriting = $copywriting;

        return $this;
    }

    public function add(string $phone, array $data)
    {
        $txt = Copywriting::text($this->copywriting)->data($data)->make()->get();
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $this->data = [
            'user_id' => $this->user_id,
            'process_id' => $this->process_id,
            // 'device_id' => $this->device->id,
            // 'judul' => $this->title,
            // 'type_message' => $this->type,
            'phone' => $phone,
            'payload' => '[]',
            // 'status' => 'creating',
            // 'priority' => $this->priority,
            // 'pause' => $this->randPause(),
            'text' => $txt,
        ];
        $this->results[] = [
            ...$this->data,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ];

        return $this;
    }

    public function setAutoCreateTable(bool $auto_create_table): self
    {
        $this->auto_create_table = $auto_create_table;

        return $this;
    }

    public function create(string $phone, array $data): self
    {
        $this->add($phone, $data);
        Messages::zu($this->user_id, $this->auto_create_table)->create($this->data);

        return $this;
    }

    public function save()
    {
        return Messages::zu($this->user_id, $this->auto_create_table)->insert($this->results);
    }
}
