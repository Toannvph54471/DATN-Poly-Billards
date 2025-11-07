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
            $table->string('reservation_code', 20)->unique();
            $table->foreignId('table_id')->constrained('tables');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null'); // Trỏ đến USERS
            $table->datetime('reservation_time');
            $table->datetime('end_time'); // Gộp
            $table->integer('duration'); // phút
            $table->integer('guest_count');
            $table->string('status')->default('Pending');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users'); // Nullable
            
            // Trường tracking (gộp)
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('no_show_at')->nullable();
            
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
