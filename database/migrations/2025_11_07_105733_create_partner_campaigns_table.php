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
        Schema::create('partner_campaigns', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->enum('frequency', ['weekly', 'biweekly', 'monthly']);
			$table->date('start_date');
			$table->date('end_date');
			$table->decimal('min_value', 12, 2)->default(0);
			$table->integer('min_quantity')->default(0);
			$table->integer('goal_quantity')->default(0);
			$table->enum('status', ['draft', 'active', 'closed'])->default('draft');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_campaigns');
    }
};
