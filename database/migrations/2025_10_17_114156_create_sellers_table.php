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
        Schema::create('sellers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contact_person');
            $table->string('email')->unique('email');
            $table->string('phone', 20)->unique('phone');
            $table->text('address')->nullable();
            $table->string('city', 150)->nullable();
            $table->string('state', 150)->nullable();
            $table->string('country', 150)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('gst_number', 50)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->string('upi_id', 100)->nullable();
            $table->enum('compliance_status', ['pending', 'verified', 'rejected'])->nullable()->default('pending');
            $table->boolean('bank_verified')->default(false)->comment('1=verified,0=not verified');
            $table->string('logo')->nullable();
            $table->json('documents')->nullable();
            $table->decimal('commission_rate', 5)->nullable()->default(10);
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
