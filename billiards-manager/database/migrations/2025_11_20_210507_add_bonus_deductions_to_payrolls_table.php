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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('bonus', 15, 2)->default(0)->after('total_amount');
            $table->decimal('deductions', 15, 2)->default(0)->after('bonus');
            $table->text('notes')->nullable()->after('deductions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['bonus', 'deductions', 'notes']);
        });
    }
};
