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
            $table->datetime('end_time');
            $table->integer('duration_minutes');
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('total_price', 10, 2);
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
