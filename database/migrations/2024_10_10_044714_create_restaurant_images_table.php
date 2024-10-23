<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
        CREATE VIEW vw_restaurant_images AS
        SELECT
        restaurants.id as restaurant_id,
        images.link_id as link_id,
        images.gener as gener,
        images.upload_url as upload_url
        FROM images
        INNER JOIN restaurants ON restaurants.id = images.link_id
        WHERE images.gener = "restaurant_image";
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DROP VIEW IF EXISTS vw_restaurant_images');

    }
};
