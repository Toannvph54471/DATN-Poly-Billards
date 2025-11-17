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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('rate_code')->nullable()->after('type');

            // Thêm foreign key nếu muốn
            $table->foreign('rate_code')
                ->references('code')
                ->on('table_rates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['rate_code']);
            $table->dropColumn('rate_code');
        });
    }
};
