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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30)->unique()->nullable(); // Key chính cho khách
            $table->string('email')->unique()->nullable(); // Nullable cho khách
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable cho khách vãng lai
            $table->foreignId('role_id')->constrained('roles');
            
            // -- Trường CRM (từ bảng customers cũ) --
            $table->string('customer_type', 50)->default('New')->nullable();
            $table->integer('total_visits')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->text('note')->nullable(); // Ghi chú của khách
            
            $table->string('remember_token', 100)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
