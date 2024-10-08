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
            $table->dropForeign(['taste_id']);
            $table->unsignedBigInteger('taste_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_restaurant', function (Blueprint $table) {
            $table->unsignedBigInteger('taste_id')->nullable(false)->change();
            $table->foreign('taste_id')->references('id')->on('tastes')->onDelete('cascade');
        });
    }
};
