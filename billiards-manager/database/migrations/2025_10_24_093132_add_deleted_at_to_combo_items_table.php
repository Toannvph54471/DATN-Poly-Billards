<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combo_items', function (Blueprint $table) {
            $table->softDeletes(); // Tự động thêm cột deleted_at (kiểu DATETIME NULL)
        });
    }

    public function down(): void
    {
        Schema::table('combo_items', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Xóa cột deleted_at khi rollback
        });
    }
};
