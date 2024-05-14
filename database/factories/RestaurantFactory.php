<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'open_time' => fake()->time($format = 'H:i:s', $max = 'now'),
            'close_time' => fake()->time($format = 'H:i:s', $max = 'now'),
            'phone_number' => fake()->e164PhoneNumber,
            
        ];
    }
}
