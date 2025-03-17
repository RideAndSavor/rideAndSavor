<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained('sub_categories')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_price', 10, 2)->default(0.00);
            $table->decimal('final_price', 10, 2);
            $table->integer('stock_quantity');
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
