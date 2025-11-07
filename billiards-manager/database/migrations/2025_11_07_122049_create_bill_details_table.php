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
        Schema::create('bill_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->foreignId('combo_id')->nullable()->constrained('combos');
            $table->foreignId('parent_bill_detail_id')->nullable()->constrained('bill_details');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->boolean('is_combo_component')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_details');
    }
};
