<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('combo_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->default(0)->after('quantity');
        });
    }

    public function down()
    {
        Schema::table('combo_items', function (Blueprint $table) {
            $table->dropColumn('unit_price');
        });
    }
};
