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
        Schema::create('cj_orders', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('local_order_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('cj_order_number')->nullable();
            $table->string('cj_orderid')->nullable();
            $table->string('logistic_name')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cj_orders');
    }
};
