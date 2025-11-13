<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            $table->integer('paused_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            $table->timestamp('paused_at')->nullable()->change();
        });
    }
};
