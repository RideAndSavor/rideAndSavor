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
        Schema::table('food_restaurant', function (Blueprint $table) {
            $table->longText('description')->after('price');
            $table->foreignId('taste_id')->references('id')->on('tastes')->onDelete('cascade')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_restaurant', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropForeign(['taste_id']);
            $table->dropColumn('taste_id');
        });
    }
};
