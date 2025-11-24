<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Nếu chưa có cột bill_id thì thêm vào
            if (!Schema::hasColumn('payments', 'bill_id')) {
                $table->foreignId('bill_id')
                    ->nullable()
                    ->after('id')  // hoặc after('reservation_id') nếu còn
                    ->constrained('bills')
                    ->onDelete('set null');
            }

            // Nếu vẫn còn cột reservation_id cũ → xóa luôn cho sạch
            if (Schema::hasColumn('payments', 'reservation_id')) {
                $table->dropForeign(['reservation_id']);
                $table->dropColumn('reservation_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'bill_id')) {
                $table->dropForeign(['bill_id']);
                $table->dropColumn('bill_id');
            }

            // Khôi phục lại cột cũ nếu cần (tùy bạn)
            // $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
