<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // File: 2025_10_31_143025_update_products_table.php
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        // XÓA cột category cũ (string)
        $table->dropColumn('category');

        // Thêm category_id
        $table->foreignId('category_id')
              ->nullable()
              ->after('name')
              ->constrained('categories')
              ->nullOnDelete();

        // Thay đổi product_type thành enum
        $table->dropColumn('product_type');
        $table->enum('product_type', ['Service', 'Consumption'])
              ->default('Consumption')
              ->after('category_id');
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropConstrainedForeignId('category_id');
        $table->dropColumn('product_type');

        // Khôi phục lại cột cũ
        $table->string('category')->after('name');
        $table->string('product_type')->default('Single')->after('category');
    });
}};
