<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // 1. Thêm cột customer_name, customer_phone vào fillable
            $table->string('customer_name')->nullable()->change();
            $table->string('customer_phone')->nullable()->change();

            // 2. Sửa payment_type từ enum sang string để linh hoạt hơn
            DB::statement("ALTER TABLE reservations MODIFY payment_type VARCHAR(20) DEFAULT 'onsite'");

            // 3. Thêm customer_email nếu chưa có
            if (!Schema::hasColumn('reservations', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            DB::statement("ALTER TABLE reservations MODIFY payment_type ENUM('full', 'deposit') DEFAULT 'deposit'");
        });
    }
};
