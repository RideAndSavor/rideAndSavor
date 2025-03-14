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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electronic_id')->constrained('electronics')->onDelete('cascade');
            $table->integer('in_stock')->default(0);
            $table->integer('out_stock')->default(0);
            $table->integer('remaining_stock')->default(0);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'low_stock'])->default('in_stock');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
