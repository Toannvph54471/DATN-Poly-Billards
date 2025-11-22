// database/migrations/2025_11_16_000001_create_payments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('VND');
            $table->string('payment_method'); // vnpay, momo, mock
            $table->enum('payment_type', ['full', 'deposit'])->default('deposit');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

            $table->string('transaction_id')->nullable()->unique();
            $table->text('payment_url')->nullable();
            $table->json('payment_data')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
