<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combo_items', function (Blueprint $table) {
            if (!Schema::hasColumn('combo_items', 'table_category_id')) {
                $table->foreignId('table_category_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('categories')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('combo_items', 'table_price_per_hour')) {
                $table->decimal('table_price_per_hour', 10, 2)->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('combo_items', function (Blueprint $table) {
            if (Schema::hasColumn('combo_items', 'table_category_id')) {
                $table->dropConstrainedForeignId('table_category_id');
            }
            if (Schema::hasColumn('combo_items', 'table_price_per_hour')) {
                $table->dropColumn('table_price_per_hour');
            }
        });
    }
};
