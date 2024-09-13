<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantTypes = [
            'Chinese Food', 'Indian Food', 'Myanmar Food (Htamin)', 'Korean Food',
            'Japanese Food', 'Thai Food', 'Vietnamese Food', 'Malaysian Food',
            'Indonesian Food', 'Filipino Food', 'Nepalese Food', 'Sri Lankan Food',
            'Lebanese Food', 'Turkish Food', 'Persian/Iranian Food', 'Israeli Food',
            'Italian Food', 'French Food', 'Spanish Food', 'Greek Food', 'German Food',
            'British Food', 'Russian Food', 'Portuguese Food', 'Ethiopian Food',
            'Moroccan Food', 'Nigerian Food', 'South African Food', 'Mexican Food',
            'Brazilian Food', 'Argentinian Food', 'Peruvian Food', 'Cuban Food',
            'American Food', 'Canadian Food', 'Cajun/Creole Food', 'Australian Food',
            'New Zealand Food'
        ];

        $currentTime = Carbon::now();

        foreach ($restaurantTypes as $type) {
            DB::table('restaurant_types')->insert([
                'name' => $type,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        }

    }
}
