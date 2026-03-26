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
        Schema::create('partner_product_import_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('partner_campaign_id');
            $table->string('product_name')->nullable();
            $table->text('reason');
            $table->text('meta')->nullable(); // optional additional details (json)
            $table->timestamps();

            $table->index(['vendor_id', 'partner_campaign_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_product_import_logs');
    }
};
