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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_price');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')
                                      ->on('users')
                                      ->onDelete('cascade')
                                      ->onUpdate('cascade');

            $table->enum('status',['In preparation','Prepared','shipped','delivered','cancelled'])->default('In preparation');
           // $table->unsignedBigInteger('driver_id');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
