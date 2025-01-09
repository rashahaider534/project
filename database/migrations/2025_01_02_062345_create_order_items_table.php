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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('product_id');   
            $table->integer('quantity')->default(0);
            $table->foreign('product_id')->references('id')
                                         ->on('products')
                                         ->onDelete('cascade')
                                         ->onUpdate('cascade');
            $table->unsignedBigInteger('order_id');
            $table->decimal('price')->default(0);//سعر المنتج وقت الطلب
            $table->foreign('order_id')->references('id')
                                         ->on('orders')
                                         ->onDelete('cascade')
                                         ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
