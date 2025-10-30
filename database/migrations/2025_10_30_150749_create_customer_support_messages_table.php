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
        Schema::create('customer_support_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('support_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['customer', 'admin']);
            $table->text('message')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps(); // ✅ Only this, no duplicate created_at or updated_at

            // Foreign key constraints
            $table->foreign('support_id')
                ->references('id')
                ->on('customer_supports')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_support_messages');
    }
};
