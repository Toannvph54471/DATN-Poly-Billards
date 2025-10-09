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
        Schema::create('reservation_logs', function (Blueprint $table) {
    $table->id('log_id');
    $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
    $table->string('action'); // created, updated, cancelled, checked_in, auto_cancelled
    $table->unsignedBigInteger('changed_by')->nullable(); // user_id
    $table->dateTime('changed_at');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_logs');
    }
};
