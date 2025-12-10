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
        Schema::table('table_rates', function (Blueprint $table) {
            $table->integer('rounding_minutes')->default(15)->after('max_hours')->comment('Làm tròn thời gian lên (phút)');
            $table->integer('rounding_amount')->default(1000)->after('rounding_minutes')->comment('Làm tròn tiền lên (đơn vị VND)');
            $table->integer('min_charge_minutes')->default(15)->after('rounding_amount')->comment('Thời gian tính phí tối thiểu (phút)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_rates', function (Blueprint $table) {
            $table->dropColumn(['rounding_minutes', 'rounding_amount', 'min_charge_minutes']);
        });
    }
};
