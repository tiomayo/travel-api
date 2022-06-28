<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(30),
            'origin' => $this->faker->city(),
            'destination' => $this->faker->city(),
            'start' => Date::now()->format('Y-m-d'),
            'end' => Date::now()->format('Y-m-d'),
            'type' => $this->faker->text(10),
            'description' => $this->faker->realText(50),
        ];
    }
}
