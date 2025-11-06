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
        Schema::table('bill_time_usage', function (Blueprint $table) {
            // 1. end_time → nullable
            $table->timestamp('end_time')->nullable()->change();

            // 2. duration_minutes → nullable
            $table->integer('duration_minutes')->nullable()->change();

            // 3. total_price → nullable + giữ default 0
            $table->decimal('total_price', 10, 2)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            // Khôi phục lại trạng thái cũ (không null)
            $table->timestamp('end_time')->nullable(false)->default(now())->change();
            $table->integer('duration_minutes')->default(0)->change();
            $table->decimal('total_price', 10, 2)->default(0)->change();
        });
    }
};