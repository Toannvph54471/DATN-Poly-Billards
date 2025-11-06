<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 1. XÓA cột cũ (kiểm tra tồn tại)
            if (Schema::hasColumn('products', 'category')) {
                $table->dropColumn('category');
            }

            // 2. Thêm category_id (chỉ nếu chưa có)
            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('name')
                    ->constrained('categories')
                    ->nullOnDelete();
            }

            // 3. Thay đổi product_type
            if (Schema::hasColumn('products', 'product_type')) {
                $table->dropColumn('product_type');
            }

            $table->enum('product_type', ['Service', 'Consumption'])
                ->default('Consumption')
                ->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Xóa category_id
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }

            // Xóa product_type mới
            if (Schema::hasColumn('products', 'product_type')) {
                $table->dropColumn('product_type');
            }

            // Khôi phục cột cũ
            if (! Schema::hasColumn('products', 'category')) {
                $table->string('category')->after('name');
            }
            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->default('Single')->after('category');
            }
        });
    }
};
