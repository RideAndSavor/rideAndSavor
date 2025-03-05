<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 ADMIN users
        User::factory()->count(10)->withRole(Config::get('variable.TWO'))->create();

        // Create 10 OWNER users
        User::factory()->count(10)->withRole(Config::get('variable.THREE'))->create();

        // Create 10 RIDER users
        User::factory()->count(10)->withRole(Config::get('variable.FOUR'))->create();

        // Create 10 DRIVER users
        User::factory()->count(50)->withRole(Config::get('variable.FIVE'))->create();

        // Create 10 default (CUSTOMER) users
        User::factory()->count(10)->withRole(Config::get('variable.ONE'))->create();
    }
}
