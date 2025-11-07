<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('combos')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();

            // CÁC CỘT SẼ BỊ XÓA SAU (nếu cần)
            $table->foreignId('table_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('table_price_per_hour', 10, 2)->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('choice_group')->nullable();
            $table->integer('max_choices')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_items');
    }
};
