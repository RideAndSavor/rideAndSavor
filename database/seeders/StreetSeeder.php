<?php

namespace Database\Seeders;

use App\Models\Street;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StreetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Street::create([
            'ward_id' => 1,
            'name' => 'first street'
        ]);
    }
}
