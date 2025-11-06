<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng giá bàn theo giờ
        Schema::create('table_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->integer('duration_minutes'); 
            $table->decimal('price_per_hour', 10, 2); 
            $table->timestamps();
            
            $table->unique(['category_id', 'duration_minutes']);
            $table->index(['category_id', 'duration_minutes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_pricing');
    }
};