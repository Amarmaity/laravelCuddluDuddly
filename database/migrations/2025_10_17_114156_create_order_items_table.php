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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index('fk_order_items_order');
            $table->unsignedBigInteger('product_id')->index('fk_order_items_product');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10)->default(0);
            $table->decimal('subtotal', 10)->nullable()->storedAs('`quantity` * `price`');
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
