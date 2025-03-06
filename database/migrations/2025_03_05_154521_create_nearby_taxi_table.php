<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nearby_taxi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->onDelete('cascade');
            $table->foreignId('taxi_driver_id')->constrained('taxi_drivers')->onDelete('cascade');
            $table->string('driver_name'); // Name from users table
            $table->string('plate_number'); // Plate number from taxi_drivers table
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nearby_taxi');
    }
};
