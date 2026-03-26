<?php
// database/migrations/xxxx_xx_xx_add_myus_columns_to_orders.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('master_order_number')->nullable()->after('order_number');
            $table->string('myus_package_status')->nullable()->after('shipping_status');
            $table->timestamp('myus_package_received_at')->nullable()->after('shipped_at');
        });

        Schema::create('myus_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('package_id');
            $table->string('tracking_number')->nullable();
            $table->decimal('weight', 10, 2);
            $table->string('weight_unit', 1);
            $table->string('package_status');
            $table->string('package_substatus')->nullable();
            $table->timestamp('arrival_date')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        Schema::create('order_myus_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('order_id')->constrained();
			$table->integer('order_product_id')->nullable();
			$table->foreign('order_product_id')
				  ->references('id')
				  ->on('order_products')
				  ->nullOnDelete();
			$table->bigInteger('myus_order_item_id');
			$table->string('myus_order_number_item_id');

			$table->timestamps();
		});
    }

    public function down()
    {
        Schema::dropIfExists('order_myus_items');
        Schema::dropIfExists('myus_packages');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['master_order_number', 'myus_package_status', 'myus_package_received_at']);
        });
    }
};