<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('table_pricing');
    }

    public function down(): void
    {
        // Nếu muốn có thể tạo lại bảng khi rollback
        Schema::create('table_pricing', function (Blueprint $table) {
            $table->id();
            // thêm các cột cũ nếu bạn nhớ, ví dụ:
            // $table->string('name');
            // $table->decimal('price_per_hour', 8, 2);
            $table->timestamps();
        });
    }
};
