<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['table', 'product']);
            $table->text('description')->nullable();
            $table->decimal('default_price', 10, 2)->nullable(); // Đảm bảo có
            $table->decimal('hourly_rate', 10, 2)->nullable();   // Sẽ dùng sau
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
