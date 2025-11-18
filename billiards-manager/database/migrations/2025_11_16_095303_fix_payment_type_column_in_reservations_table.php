<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Đổi sang VARCHAR
            $table->string('payment_type', 20)
                ->default('onsite')
                ->change();
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('payment_type', ['online', 'onsite'])
                ->default('onsite')
                ->change();
        });
    }
};
