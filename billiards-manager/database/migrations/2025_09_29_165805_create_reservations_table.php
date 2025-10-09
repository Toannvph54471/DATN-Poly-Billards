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
        Schema::create('reservations', function (Blueprint $table) {
    $table->id('id');
    $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
    $table->dateTime('reservation_time');
    $table->integer('duration');
    $table->string('status'); // pending, checked_in, cancelled, expired
    $table->text('note')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
