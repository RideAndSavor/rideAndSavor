<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RestaurantTypesSeeder::class,
            CountrySeeder::class,
            RoleSeeder::class,
            SalarySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            TownshipSeeder::class,
            WardSeeder::class,
            StreetSeeder::class,
            TaxiDriverSeeder::class
        ]);
    }
}
