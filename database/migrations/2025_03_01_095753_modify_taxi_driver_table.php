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
            // Add latitude and longitude columns after user_id
            $table->decimal('latitude', 10, 7)->nullable()->after('user_id');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            // Drop the current_location column
            $table->dropColumn('current_location');

            // Add driver_license_number column after license_plate
            $table->string('driver_license_number')->nullable()->after('license_plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxi_drivers', function (Blueprint $table) {
            // Drop the newly added columns
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('driver_license_number');

            // Add back the current_location column
            $table->string('current_location')->nullable();
        });
    }
};
