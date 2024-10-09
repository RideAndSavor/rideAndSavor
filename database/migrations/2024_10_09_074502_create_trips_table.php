<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users');  // Foreign Key to users table (rider)
            $table->foreignId('selected_driver_id')->nullable()->constrained('taxi_drivers');  
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('pending');  // Enum for trip status
            $table->json('current_location');  // JSON field for the rider's current location
            $table->json('destination');  // JSON field for the rider's destination
            $table->json('driver_location')->nullable();  // JSON field for the driver's current location
            $table->decimal('price', 10, 2)->default('0.0');  // Decimal for trip price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
