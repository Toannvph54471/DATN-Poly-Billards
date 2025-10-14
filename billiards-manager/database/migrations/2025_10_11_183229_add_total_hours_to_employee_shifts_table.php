<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalHoursToEmployeeShiftsTable extends Migration
{
    public function up()
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            $table->decimal('total_hours', 8, 2)->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            $table->dropColumn('total_hours');
        });
    }
}