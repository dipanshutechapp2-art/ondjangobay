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
        Schema::create('dropship_orders', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('dropship_product_id');
			$table->string('customer_name');
			$table->integer('quantity');
			$table->timestamps();
			$table->foreign('dropship_product_id')->references('id')->on('dropship_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dropship_orders');
    }
};
