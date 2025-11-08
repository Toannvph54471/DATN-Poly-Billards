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
        Schema::create('bill_time_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills');
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable(); // Gộp
            $table->integer('duration_minutes')->nullable(); // Gộp
            $table->decimal('hourly_rate', 10, 2)->default(0); // Gộp
            $table->decimal('total_price', 10, 2)->nullable()->default(0); // Gộp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_time_usage');
    }
};
