<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeAttendanceForLocationStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->integer('clock_in_location')->default(0)->index();
            $table->integer('clock_out_location')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn('clock_in_location');
            $table->dropColumn('clock_out_location');
        });
    }
}
