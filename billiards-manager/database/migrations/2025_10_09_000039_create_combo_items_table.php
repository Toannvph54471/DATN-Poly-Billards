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
       Schema::create('combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('combos');
            $table->foreignId('product_id')
            ->constrained('products')
             ->onDelete('cascade'); // ✅ Xóa product thì tự động gỡ khỏi combo_item
            $table->integer('quantity');
            $table->boolean('is_required')->default(true);
            $table->string('choice_group')->nullable();
            $table->integer('max_choices')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_items');
    }
};
