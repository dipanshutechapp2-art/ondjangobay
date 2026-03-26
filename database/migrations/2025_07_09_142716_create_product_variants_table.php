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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('product_attribute_id')->nullable();
			$table->string('value')->nullable();
			$table->decimal('price', 10, 2)->nullable();
			$table->string('sku')->nullable();
			$table->string('image')->nullable();
			$table->integer('stock')->nullable();
            $table->timestamps();
			$table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
