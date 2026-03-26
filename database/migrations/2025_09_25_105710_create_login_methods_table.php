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
        Schema::create('login_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Human readable e.g. "Google Login"
            $table->string('code')->unique(); // slug/code e.g. google, facebook, email
            $table->boolean('is_active')->default(true); // can be enabled/disabled
            $table->timestamps();
        });
		
		\DB::table('login_methods')->insert([
            ['name' => 'Email & Password', 'code' => 'email', 'is_active' => true],
            ['name' => 'Phone & OTP', 'code' => 'phone', 'is_active' => true],
            ['name' => 'Google', 'code' => 'google', 'is_active' => true],
            ['name' => 'Facebook', 'code' => 'facebook', 'is_active' => true],
            ['name' => 'Apple', 'code' => 'apple', 'is_active' => true],
            ['name' => 'Face Recognition', 'code' => 'face', 'is_active' => false],
            ['name' => 'Biometric / Fingerprint', 'code' => 'biometric', 'is_active' => false],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_methods');
    }
};
