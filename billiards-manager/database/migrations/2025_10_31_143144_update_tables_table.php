<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // 1. Thêm cột capacity (vì chưa có)
            $table->integer('capacity')->after('table_name')->default(4);

            $table->foreignId('category_id')
                ->after('table_number')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->dropColumn('status'); // Xóa cột cũ
            $table->enum('status', ['available', 'occupied', 'maintenance'])
                ->default('available')
                ->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn('capacity');

            $table->dropColumn('status');
            $table->string('status')->default('Available')->after('type');
        });
    }
};
