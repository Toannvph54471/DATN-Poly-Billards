<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kiểm tra tables có category_id chưa
        $missing = DB::table('tables')->whereNull('category_id')->count();
        if ($missing > 0) {
            throw new \Exception("Có {$missing} bàn chưa có category_id!");
        }

        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
        });

        // Cập nhật hourly_rate cho categories
        DB::statement("
            UPDATE categories 
            SET hourly_rate = COALESCE(hourly_rate, default_price, 50000)
            WHERE type = 'table' AND hourly_rate IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('status');
        });

        DB::statement("
            UPDATE tables t
            INNER JOIN categories c ON t.category_id = c.id
            SET t.hourly_rate = c.hourly_rate
            WHERE c.type = 'table'
        ");
    }
};