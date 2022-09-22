<?php

namespace DavidArl\WaFiture\Database\Factories;

use DavidArl\WaFiture\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition()
    {
        $isMale = rand(1, 0);
        $gender = $isMale ? 'male' : 'female';
        return [
            'user_id' => 1,
            'phone' => $this->faker->phoneNumber(),
            'sapaan' => $isMale ? 'Bpk' : 'Ibu',
            'panggilan' => $this->faker->firstName($gender),
            'nama' => $this->faker->name($gender),
            'email' => $this->faker->email,
            'tgl_lahir' => $this->faker->date('Y-m-d', 'now'),
            'gender' => $gender,
            'provinsi' => $this->faker->city(),
            'kota' => $this->faker->city(),
            'kecamatan' => $this->faker->citySuffix(),
        ];
    }
}
