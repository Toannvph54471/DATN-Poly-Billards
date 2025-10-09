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
        Schema::create('bill_timeblocks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->integer('duration_minutes');
    $table->decimal('rate', 10, 2);
    $table->decimal('total_price', 10, 2);
    $table->foreignId('applied_promotion_id')->nullable()->constrained('promotions')->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_timeblocks');
    }
};
