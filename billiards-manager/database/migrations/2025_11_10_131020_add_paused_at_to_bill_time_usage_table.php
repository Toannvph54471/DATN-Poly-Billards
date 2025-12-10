<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            $table->timestamp('paused_at')->nullable()->after('end_time');
            $table->integer('paused_duration')->default(0)->after('paused_at');
        });
    }

    public function down()
    {
        Schema::table('bill_time_usage', function (Blueprint $table) {
            $table->dropColumn(['paused_at', 'paused_duration']);
        });
    }
};
