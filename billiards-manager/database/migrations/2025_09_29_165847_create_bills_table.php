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
    $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
    $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
    $table->dateTime('start_time');
    $table->dateTime('end_time')->nullable();
    $table->decimal('total_amount', 10, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('final_amount', 10, 2)->default(0);
    $table->foreignId('promotion_id')->nullable()->constrained('promotions')->onDelete('set null');
    $table->foreignId('combo_id')->nullable()->constrained('combos')->onDelete('set null');
    $table->string('status'); // Open, Closed, Cancelled
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
