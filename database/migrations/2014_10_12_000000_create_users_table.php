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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('phone')->unique();
            $table->string('password');
            $table->string('Firstname')->nullable(); 
            $table->string('Lastname')->nullable();
            $table->string('role')->default('user');
            $table->string('location')->nullable();   
            $table->timestamp('phone_verified_at')->nullable();           
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
