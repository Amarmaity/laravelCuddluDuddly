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
        Schema::table('seller_supports', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_supports', 'closed_by')) {
                $table->foreignId('closed_by')->nullable()->constrained('admin_users')->nullOnDelete();
            }

            if (!Schema::hasColumn('seller_supports', 'reopened_by')) {
                $table->foreignId('reopened_by')->nullable()->constrained('admin_users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_supports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('closed_by');
            $table->dropConstrainedForeignId('reopened_by');
        });
    }
};
