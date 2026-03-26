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
			$table->string('order_id');
			$table->foreignId('vendor_id')->constrained('users')->onDelete('cascade'); 
			$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
			$table->decimal('vendor_amount', 10, 2);
			$table->decimal('ondjango_commission', 10, 2);
			$table->decimal('commission_rate', 10, 2)->default(0);
			$table->string('payment_method');
			$table->string('transaction_id')->nullable();
			$table->enum('status', ['paid', 'settled', 'refunded'])->default('paid');
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
