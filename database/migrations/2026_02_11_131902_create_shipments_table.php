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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('vendor_id')->nullable();

            $table->string('carrier'); // MYUS, DHL, etc
            $table->string('external_order_id')->nullable();
            $table->string('external_shipment_id')->nullable();

            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();

            $table->string('shipment_status')->nullable(); 
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->json('raw_response')->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('vendor_id');
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
