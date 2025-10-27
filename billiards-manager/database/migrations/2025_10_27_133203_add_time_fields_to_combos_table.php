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
       Schema::table('combos', function (Blueprint $table) {
    $table->boolean('is_time_combo')->default(false)->after('actual_value'); //xác định combo có thời gian chơi hay không
    $table->integer('play_duration_minutes')->nullable()->after('is_time_combo'); // thời gian chơi tính theo phút
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combos', function (Blueprint $table) {
            //
        });
    }
};
