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
        Schema::create('cart_item_product', function (Blueprint $table) {
            $table->id(); // مفتاح أساسي للجدول
            $table->unsignedBigInteger('cart_item_id'); // عمود المفتاح الأجنبي لـ cart_ite
            $table->unsignedBigInteger('product_id'); // عمود المفتاح الأجنبي لـ product
            $table->integer('quantity')->default(0);
            $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade')->onUpdate('cascade'); // ربط المفتاح الأجنبي بـ cart_items
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade'); // ربط المفتاح الأجنبي بـ products
            $table->timestamps(); // إضافي للطوابع الزمنية created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_product');
    }
};
