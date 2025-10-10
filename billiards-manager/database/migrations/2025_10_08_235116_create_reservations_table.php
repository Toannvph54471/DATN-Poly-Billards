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
            $table->id();
            $table->foreignId('table_id')->constrained('tables');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->datetime('reservation_time');
            $table->integer('duration'); // phút
            $table->integer('guest_count');
            $table->string('status')->default('Pending'); // Pending, Confirmed, CheckedIn, Cancelled, Expired
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
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
