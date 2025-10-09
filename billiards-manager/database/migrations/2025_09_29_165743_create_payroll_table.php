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
        Schema::create('payroll', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
    $table->string('month'); // 2025-09
    $table->integer('total_shifts');
    $table->decimal('total_hours', 10, 2);
    $table->decimal('base_salary', 10, 2);
    $table->decimal('bonus', 10, 2)->default(0);
    $table->decimal('penalty', 10, 2)->default(0);
    $table->decimal('final_salary', 10, 2);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
