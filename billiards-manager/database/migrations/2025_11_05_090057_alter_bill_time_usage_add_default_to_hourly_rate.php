<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            // Đặt default = 0, cho phép NULL (nếu muốn linh hoạt)
            $table->decimal('hourly_rate', 10, 2)
                ->default(0)
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            // Khôi phục: không default, không null
            $table->decimal('hourly_rate', 10, 2)
                ->nullable(false)
                ->change();
        });
    }
};
