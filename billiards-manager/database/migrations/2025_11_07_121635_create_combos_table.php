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
            $table->boolean('is_time_combo')->default(false);
            $table->integer('play_duration_minutes')->nullable();
            $table->foreignId('table_rate_id')->nullable()->constrained('table_rates')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
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
