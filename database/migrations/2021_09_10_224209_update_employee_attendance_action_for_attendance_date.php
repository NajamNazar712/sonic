<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeAttendanceActionForAttendanceDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_attendance_action_logs', function (Blueprint $table) {
            $table->date('attendance_date')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_attendance_action_logs', function (Blueprint $table) {
            $table->dropColumn('attendance_date');
        });
    }
}
