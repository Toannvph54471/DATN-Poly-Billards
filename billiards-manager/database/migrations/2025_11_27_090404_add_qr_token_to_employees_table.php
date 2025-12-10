<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'qr_token')) {
                $table->string('qr_token')->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('employees', 'qr_token_expires_at')) {
                $table->timestamp('qr_token_expires_at')->nullable()->after('qr_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_token_expires_at']);
        });
    }
};
