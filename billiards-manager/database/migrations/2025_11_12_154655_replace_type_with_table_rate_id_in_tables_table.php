<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Xóa cột 'type'
            $table->dropColumn('type');

            // Thêm cột table_rate_id với khóa ngoại
            $table->foreignId('table_rate_id')
                  ->nullable()
                  ->constrained('table_rates')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Xóa foreign key và cột table_rate_id
            $table->dropForeign(['table_rate_id']);
            $table->dropColumn('table_rate_id');

            // Thêm lại cột type (string)
            $table->string('type')->after('table_name');
        });
    }
};
