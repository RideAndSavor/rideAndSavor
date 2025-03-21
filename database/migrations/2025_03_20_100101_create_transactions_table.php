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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('product_orders')->onDelete('cascade'); // Links to Order
            $table->string('transaction_id')->unique(); // Stripe or Payment Gateway Transaction ID
            $table->decimal('amount', 10, 2); // Total amount paid
            $table->string('currency', 10)->default('USD'); // Currency of payment
            $table->string('payment_method'); // Stripe, PayPal, etc.
            $table->string('status'); // pending, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
