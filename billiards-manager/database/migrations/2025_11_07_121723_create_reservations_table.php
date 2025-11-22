// database/migrations/2025_11_07_121723_create_reservations_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 20)->unique();
            $table->foreignId('table_id')->constrained('tables');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->dateTime('reservation_time');
            $table->dateTime('end_time');
            $table->integer('duration'); // phút
            $table->integer('guest_count');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0); // đặt cọc 100%
            $table->string('status')->default('pending'); // pending, confirmed, checked_in, completed, cancelled, no_show
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');

            // Tracking
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('no_show_at')->nullable();

            // Payment (100% online)
            $table->enum('payment_type', ['full', 'deposit'])->default('deposit')
                ->comment('full: thanh toán 100%, deposit: đặt cọc 100%');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_gateway')->nullable(); // vnpay, momo, mock
            $table->string('transaction_id')->nullable()->unique();
            $table->text('payment_url')->nullable();
            $table->timestamp('payment_completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
