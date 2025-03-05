<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxiDriver;
use App\Models\User;

class TaxiDriverSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure that we have at least 10 users with the 'driver' role
        $drivers = User::factory()->count(10)->create([
            'role' => 5,  // Ensure these users are marked as 'driver'
        ]);

        // Create 10 taxi drivers linked to the created drivers
        foreach ($drivers as $driver) {
            TaxiDriver::factory()->create([
                'user_id' => $driver->id,  // Link the created user to taxi driver
            ]);
        }
    }
}
