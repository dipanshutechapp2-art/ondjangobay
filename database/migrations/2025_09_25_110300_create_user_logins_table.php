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
        Schema::create('user_logins', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('login_method_id');
			$table->longText('identifier');
			$table->longText('secret')->nullable(); 
			$table->boolean('is_primary')->default(false);
			$table->timestamps();

			$table->foreign('user_id')
				  ->references('id')
				  ->on('users')
				  ->cascadeOnDelete();

			$table->foreign('login_method_id')
				  ->references('id')
				  ->on('login_methods')
				  ->cascadeOnDelete();

			$table->string('identifier_hash', 64)->nullable(); 
			$table->unique(['login_method_id', 'identifier_hash'])->nullable(); 
		});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
