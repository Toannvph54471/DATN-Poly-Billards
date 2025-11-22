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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Admin, Manager, Employee, Customer
            $table->string('slug')->unique(); // admin, manager, employee, customer
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Thêm từ file sau của bạn
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
