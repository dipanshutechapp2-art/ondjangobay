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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
			$table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
			$table->string('action')->nullable();         // e.g. "Search", "Logout"
			$table->text('details')->nullable(); // e.g. "Query: shoes"
			$table->ipAddress('ip_address')->nullable();
			$table->string('user_agent')->nullable();
			$table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
