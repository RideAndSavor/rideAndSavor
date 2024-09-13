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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->foreignId('restaurant_type_id')->nullable()->after('name')->constrained('restaurant_types')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Drop foreign key constraint and the column
            $table->dropForeign(['restaurant_type_id']); // Drops the foreign key
            $table->dropColumn('restaurant_type_id'); // Drops the restaurant_type_id column
        });

    }
};
