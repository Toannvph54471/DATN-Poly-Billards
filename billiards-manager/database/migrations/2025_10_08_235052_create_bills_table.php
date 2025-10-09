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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique(); // HD001
            $table->foreignId('table_id')->constrained('tables');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('staff_id')->constrained('users');
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('Pending');
            $table->string('status')->default('Open'); // Open, Closed, Cancelled
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
