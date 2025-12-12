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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Người thực hiện
            $table->string('action'); // Ví dụ: check_in, check_out, calculate_payroll
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->json('details')->nullable(); // Lưu dữ liệu cũ/mới nếu cần
            $table->string('ip_address')->nullable();
            $table->timestamps();

             // Không bắt buộc khoá ngoại để tránh lỗi nếu user bị xoá, chỉ cần lưu log
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
