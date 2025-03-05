<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TaxiDriver;
use App\Models\User;

class TaxiDriverFactory extends Factory
{
    protected $model = TaxiDriver::class;

    public function definition(): array
    {
        // Generate dynamic latitude and longitude values
        $latitude = fake()->latitude();
        $longitude = fake()->longitude();

        return [
            'user_id' => User::factory()->create(['role' => 5])->id, // Create user with role 5 // Dynamically assign the user's id
            'latitude' => $latitude,  // Store latitude directly
            'longitude' => $longitude,  // Store longitude directly
            'is_available' => true,
            'car_year' => fake()->numberBetween(2015, 2023),
            'car_make' => 'Toyota',
            'car_model' => 'Camry',
            'car_colour' => fake()->safeColorName(),
            'license_plate' => strtoupper(fake()->bothify('??###')),
            'driver_license_number' => strtoupper(fake()->bothify('DL-#######')),
            'other_info' => 'Experienced driver with 5-star rating',
        ];
    }
}

