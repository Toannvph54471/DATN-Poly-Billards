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
      Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('combo_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('actual_value', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->boolean('is_time_combo')->default(false); // Combo có giờ chơi bàn?
            $table->integer('play_duration_minutes')->nullable(); // Số phút chơi
            $table->foreignId('table_category_id')->nullable()->constrained('categories')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('is_time_combo');
            $table->index('table_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos');
    }
};
