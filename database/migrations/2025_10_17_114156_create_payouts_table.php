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
        Schema::create('payouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('seller_id')->comment('sellers.id');
            $table->unsignedBigInteger('initiated_by')->nullable()->index('payouts_initiated_by_foreign')->comment('admin_users.id');
            $table->decimal('amount', 14);
            $table->string('currency', 10)->default('INR');
            $table->enum('status', ['initiated', 'pending', 'processing', 'completed', 'failed', 'cancelled'])->default('initiated');
            $table->string('method')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_payout_id')->nullable();
            $table->json('beneficiary_snapshot')->nullable();
            $table->json('provider_response')->nullable();
            $table->decimal('fee', 12)->default(0);
            $table->string('idempotency_key')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
