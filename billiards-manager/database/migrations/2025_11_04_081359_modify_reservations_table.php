<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
      public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Thêm cột mới
            $table->string('reservation_code', 20)->unique()->after('id');
            $table->datetime('end_time')->after('reservation_time');
            $table->timestamp('checked_in_at')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('checked_in_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->timestamp('no_show_at')->nullable()->after('cancellation_reason');
            
            // Xóa cột thừa
            $table->dropColumn(['customer_name', 'customer_phone']);
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Khôi phục cột đã xóa
            $table->string('customer_name');
            $table->string('customer_phone');
            
            // Xóa cột đã thêm
            $table->dropColumn([
                'reservation_code',
                'end_time',
                'checked_in_at',
                'cancelled_at',
                'cancellation_reason',
                'no_show_at'
            ]);
        });
    }
};
