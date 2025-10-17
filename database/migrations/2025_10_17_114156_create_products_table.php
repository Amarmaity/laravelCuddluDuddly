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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('seller_id')->index('seller_id');
            $table->string('name');
            $table->string('slug')->unique('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10)->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('is_approved')->default(false)->comment('1=Approved, 0=Pending');
            $table->unsignedBigInteger('approved_by')->nullable()->index('approved_by');
            $table->dateTime('approved_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
