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
     Schema::create('combo_time_usages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('combo_id')->constrained('combos'); // liên kết đến combo
    $table->foreignId('bill_id')->constrained('bills'); // liên kết đến hóa đơn sử dụng combo
    $table->datetime('start_time')->nullable(); // thời gian bắt đầu sử dụng combo
    $table->datetime('end_time')->nullable(); // thời gian kết thúc sử dụng combo
    $table->integer('total_minutes')->default(0); // tổng thời gian sử dụng tính theo phút
    $table->integer('remaining_minutes')->default(0); // thời gian còn lại tính theo phút
    $table->boolean('is_expired')->default(false); // trạng thái hết hạn sử dụng combo
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_time_usages');
    }
};
