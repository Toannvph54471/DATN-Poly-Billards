<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_time_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('combos')->cascadeOnDelete();
            $table->foreignId('bill_id')->constrained('bills')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('tables')->cascadeOnDelete();

            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();

            $table->integer('total_minutes')->default(0);
            $table->integer('remaining_minutes')->default(0);
            $table->integer('extra_minutes_added')->default(0);

            $table->boolean('is_expired')->default(false);
            $table->decimal('extra_charge', 10, 2)->nullable();

            $table->datetime('warning_sent_at')->nullable();
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['combo_id', 'bill_id', 'table_id']);
            $table->index(['is_expired', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_time_usages');
    }
};
