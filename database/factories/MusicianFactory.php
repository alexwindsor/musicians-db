<?php

namespace Database\Factories;

use App\Models\Musician;
use Illuminate\Database\Eloquent\Factories\Factory;

class MusicianFactory extends Factory
{

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];
    }
}
