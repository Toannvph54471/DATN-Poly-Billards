<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tắt kiểm tra foreign key constraints để tránh lỗi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Xóa khóa ngoại trước khi xóa bảng
        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'reservation_id')) {
                // Xóa foreign key constraint trước
                $table->dropForeign(['reservation_id']);
                $table->dropColumn('reservation_id');
            }
        });

        // Xóa khóa ngoại từ reservation_status_histories
        Schema::table('reservation_status_histories', function (Blueprint $table) {
            if (Schema::hasColumn('reservation_status_histories', 'reservation_id')) {
                $table->dropForeign(['reservation_id']);
            }
            if (Schema::hasColumn('reservation_status_histories', 'changed_by')) {
                $table->dropForeign(['changed_by']);
            }
        });

        // Xóa khóa ngoại từ reservations
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'table_id')) {
                $table->dropForeign(['table_id']);
            }
            if (Schema::hasColumn('reservations', 'customer_id')) {
                $table->dropForeign(['customer_id']);
            }
            if (Schema::hasColumn('reservations', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
        });

        // Xóa khóa ngoại từ promotion_products và promotion_combo
        Schema::table('promotion_products', function (Blueprint $table) {
            if (Schema::hasColumn('promotion_products', 'promotion_id')) {
                $table->dropForeign(['promotion_id']);
            }
            if (Schema::hasColumn('promotion_products', 'product_id')) {
                $table->dropForeign(['product_id']);
            }
        });

        Schema::table('promotion_combo', function (Blueprint $table) {
            if (Schema::hasColumn('promotion_combo', 'promotion_id')) {
                $table->dropForeign(['promotion_id']);
            }
            if (Schema::hasColumn('promotion_combo', 'combo_id')) {
                $table->dropForeign(['combo_id']);
            }
        });

        // 2. Xóa các bảng theo đúng thứ tự phụ thuộc
        Schema::dropIfExists('reservation_status_histories');
        Schema::dropIfExists('reservations');
        
        // Xóa các bảng permission-related
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        
        // Xóa các bảng khác
        Schema::dropIfExists('payroll'); // Xóa payroll cũ
        Schema::dropIfExists('billiard_services');
        
        // Xóa các bảng promotion (giữ lại promotion_applications)
        Schema::dropIfExists('promotion_products');
        Schema::dropIfExists('promotion_combo');

        // Xóa cột category_id từ bảng tables (nếu còn tồn tại)
        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });

        // Xóa cột type từ bảng tables (nếu còn tồn tại)
        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'type')) {
                $table->dropColumn('type');
            }
        });

        // Xóa reservation_id từ payments (nếu còn tồn tại)
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'reservation_id')) {
                $table->dropForeign(['reservation_id']);
                $table->dropColumn('reservation_id');
            }
        });

        // Bật lại kiểm tra foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không khuyến nghị rollback vì đây là cleanup
        // Nếu cần rollback, bạn sẽ phải tạo lại tất cả các bảng và cột đã xóa
    }
};