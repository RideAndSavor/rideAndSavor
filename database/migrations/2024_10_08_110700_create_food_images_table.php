<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
        CREATE VIEW vw_food_images AS
        SELECT
        food.id as food_id,
        images.link_id as link_id,
        images.gener as gener,
        images.upload_url as upload_url
        FROM images
        INNER JOIN food ON food.id = images.link_id
        WHERE images.gener = "food_image";
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DROP VIEW IF EXISTS vw_food_images');
    }
};
