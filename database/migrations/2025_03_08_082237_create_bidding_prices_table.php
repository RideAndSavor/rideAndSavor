<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bidding_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->onDelete('cascade');
            $table->foreignId('taxi_driver_id')->constrained('taxi_drivers')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_prices');
    }
};
