<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionFieldsToTablesTable extends Migration
{
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->integer('position_x')->nullable()->after('table_rate_id');
            $table->integer('position_y')->nullable()->after('position_x');
            $table->integer('z_index')->default(0)->after('position_y');
        });
    }

    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['position_x', 'position_y', 'z_index']);
        });
    }
}