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
        Schema::create('discount_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('percentage_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('percentage_id')->references('id')->on('percentages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_items');
    }
};
