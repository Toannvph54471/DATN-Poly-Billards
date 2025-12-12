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
        // 1. Update Payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('status');
            }
            if (!Schema::hasColumn('payrolls', 'locked_at')) {
                $table->datetime('locked_at')->nullable()->after('is_locked');
            }
        });

        // 2. Update Attendance (Ensure columns exist)
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'late_reason')) {
                $table->string('late_reason')->nullable()->after('late_minutes');
            }
             if (!Schema::hasColumn('attendance', 'approval_status')) {
                $table->string('approval_status')->default('none')->after('status'); // none, pending, approved, rejected
            }
            if (!Schema::hasColumn('attendance', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
             if (!Schema::hasColumn('attendance', 'approved_at')) {
                $table->datetime('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'locked_at']);
        });

        Schema::table('attendance', function (Blueprint $table) {
             // Cẩn thận khi drop nếu cột đã tồn tại từ trước, nhưng ở đây migration thêm vào
             // nên có thể drop an toàn (hoặc check sự tồn tại).
             $table->dropColumn(['late_reason', 'approval_status', 'approved_by', 'approved_at']);
        });
    }
};
