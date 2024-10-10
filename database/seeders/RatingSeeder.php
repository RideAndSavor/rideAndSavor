<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stars = [1, 2, 3, 4, 5];
        foreach ($stars as $value) {
            $rating = new Rating();
            $rating->star = $value;
            $rating->save();
        }
    }
}
