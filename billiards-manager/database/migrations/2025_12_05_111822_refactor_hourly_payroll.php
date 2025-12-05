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
        // Employees
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->default(25000.00)->after('position');
            }
        });

        // Attendance
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'late_minutes')) {
                $table->integer('late_minutes')->default(0)->after('check_out');
                $table->integer('early_minutes')->default(0)->after('late_minutes');
                $table->text('late_reason')->nullable()->after('early_minutes');
                $table->enum('approval_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('late_reason');
                $table->foreignId('approved_by')->nullable()->constrained('users')->after('approval_status');
                $table->timestamp('approved_at')->nullable()->after('approved_by');
                $table->integer('total_minutes')->default(0)->after('approved_at');
            }
        });

        // Payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'total_minutes')) {
                $table->integer('total_minutes')->default(0)->after('period');
            }
            if (!Schema::hasColumn('payrolls', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->default(0)->after('total_minutes');
            }
            if (!Schema::hasColumn('payrolls', 'bonus')) {
                $table->decimal('bonus', 10, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('payrolls', 'penalty')) {
                $table->decimal('penalty', 10, 2)->default(0)->after('bonus');
            }
            if (!Schema::hasColumn('payrolls', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->default(0)->after('penalty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'late_minutes',
                'early_minutes',
                'late_reason',
                'approval_status',
                'approved_by',
                'approved_at',
                'total_minutes'
            ]);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'total_minutes',
                'hourly_rate',
                'bonus',
                'penalty',
                'final_amount'
            ]);
        });
    }
};
