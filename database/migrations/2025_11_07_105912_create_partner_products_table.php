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
        Schema::create('partner_products', function (Blueprint $table) {
            $table->id();
			$table->foreignId('partner_campaign_id')->constrained()->cascadeOnDelete();
			$table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
			$table->foreignId('store_id')->nullable()->constrained('vendor_stores')->cascadeOnDelete();
			$table->string('name');
			$table->text('description')->nullable();
			$table->string('image')->nullable();
			$table->decimal('old_price', 12, 2);
			$table->decimal('new_price', 12, 2);
			$table->integer('min_quantity')->default(1);
			$table->integer('max_quantity')->nullable();
			$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_products');
    }
};
