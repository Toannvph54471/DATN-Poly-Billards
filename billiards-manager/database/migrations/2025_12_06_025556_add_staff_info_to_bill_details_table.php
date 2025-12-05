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
        Schema::table('bill_details', function (Blueprint $table) {
            // Thêm cột added_by để lưu ID nhân viên thêm sản phẩm
            $table->unsignedBigInteger('added_by')->nullable()->after('is_combo_component')->comment('ID nhân viên thêm sản phẩm');
            
            // Thêm cột added_at để lưu thời gian thêm
            $table->timestamp('added_at')->nullable()->after('added_by')->comment('Thời gian thêm sản phẩm');
            
            // Thêm khóa ngoại tham chiếu đến bảng users
            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_details', function (Blueprint $table) {
            // Xóa khóa ngoại trước khi xóa cột
            $table->dropForeign(['added_by']);
            
            // Xóa các cột đã thêm
            $table->dropColumn(['added_by', 'added_at']);
        });
    }
};