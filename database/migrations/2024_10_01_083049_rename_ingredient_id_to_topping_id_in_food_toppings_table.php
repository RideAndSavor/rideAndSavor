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
        Schema::table('food_toppings', function (Blueprint $table) {
            $table->renameColumn('ingredient_id', 'topping_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_toppings', function (Blueprint $table) {
            $table->renameColumn('topping_id', 'ingredient_id');
        });
    }
};
