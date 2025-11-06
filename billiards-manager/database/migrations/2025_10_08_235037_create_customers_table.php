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
        Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone')->unique(); // Thêm unique để tránh trùng
    $table->string('email')->nullable();
    $table->string('customer_type')->default('New'); // Regular, VIP, New
    $table->integer('total_visits')->default(0);
    $table->decimal('total_spent', 12, 2)->default(0);
    $table->timestamp('last_visit_at')->nullable(); // Thêm trường này
    $table->text('note')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
