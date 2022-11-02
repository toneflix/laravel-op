<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('username');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('about')->nullable();
            $table->timestamp('dob')->nullable();
            $table->string('status_message')->nullable();
            $table->string('image')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('password');
            $table->boolean('verified')->default(false);
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->json('access_data')->nullable();
            $table->json('privileges')->nullable();
            $table->json('settings')->nullable();
            $table->string('window_token')->nullable();
            $table->string('email_verify_code')->nullable();
            $table->string('phone_verify_code')->nullable();
            $table->timestamp('last_attempt')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};