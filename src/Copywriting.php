<?php

namespace DavidArl\WaFiture;

use Carbon\Carbon;

/**
 * Easy And Fast Saving Message to table
 */
class Copywriting
{
    private string $prefix_variable;

    private bool $with_time_data = false;

    private string $copywriting;

    private array $data = [];

    private string $text_message = '';

    public function __construct(?string $prefix_variable = null)
    {
        $this->setVarPrefix($prefix_variable);
    }

    public static function init(?string $prefix_variable = null)
    {
        return new self($prefix_variable);
    }

    public function text($text): self
    {
        if (is_array($text)) {
            $text = self::printText($text);
        }
        $this->copywriting = $text;
        return $this;
    }

    public function withTimeData(): self
    {
        if ($this->with_time_data) {
            return $this;
        }
        $this->with_time_data = true;
        $dayTime = self::currentDayTime();
        $now = Carbon::now();
        $time_data = [
            '_dayName' => strtolower($now->dayName),
            '_monthName' => strtolower($now->monthName),
            '_DayName' => $now->dayName,
            '_MonthName' => $now->monthName,
            '_hours' => $now->format('H:i'),
            '_day' => $now->day,
            '_month' => $now->month,
            '_year' => $now->year,
            '_time' => $dayTime,
            '_Time' => ucfirst($dayTime),
        ];
        if (count($this->data) > 0) {
            $this->data = [...$time_data, ...$this->data];
        } else {
            $this->data = $time_data;
        }

        return $this;
    }

    public function data(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Make/Building Copywriting to Final Text
     */
    public function make(): self
    {
        $this->text_message = $this->copywriting;
        foreach ($this->data as $key => $value) {
            $key = str_replace('var', $key, $this->getVarPrefix());
            $this->text_message = str_replace($key, $value, $this->text_message);
        }
        $this->text_message = self::spintext($this->text_message);

        return $this;
    }

    public function get(): string
    {
        return $this->text_message;
    }

    public function setVarPrefix(?string $prefix): self
    {
        if ($prefix === null) {
            $this->prefix_variable = config('wa-fiture.prefix-variable') ?? ':var';
        } else {
            $this->prefix_variable = $prefix;
        }

        return $this;
    }

    public function getVarPrefix(): string
    {
        return $this->prefix_variable;
    }

    public function getCopywriting(): string
    {
        return $this->copywriting;
    }

    public static function spintext($text)
    {
        return preg_replace_callback('/{(.*?)}/', function ($match) {
            $words = explode('|', $match[1]);

            return $words[array_rand($words)];
        }, $text);
    }

    /**
     * Make Text including enter from Array
     */
    public static function printText(array $string): string
    {
        $text = '';
        $first = true;
        foreach ($string as $s) {
            ($first) ?
                $first = false :
                $text .= "\n";

            $text .= "{$s}";
        }

        return $text;
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
        } elseif ($hour >= 11 && $hour <= 15) {
            return $textTime['afternoon'][$local];
        } elseif ($hour >= 15 && $hour <= 20) {
            return $textTime['evening'][$local];
        } else {
            return $textTime['night'][$local];
        }
    }
}
