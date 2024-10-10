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
        Schema::table('taxi_drivers', function (Blueprint $table) {
            $table->integer('car_year')->nullable();
            $table->string('car_make', 255)->nullable();
            $table->string('car_model', 255)->nullable();
            $table->string('car_colour', 50)->nullable();
            $table->string('license_plate', 50)->nullable();
            $table->text('other_info')->nullable();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxi_drivers', function (Blueprint $table) {
            //
        });
    }
};
