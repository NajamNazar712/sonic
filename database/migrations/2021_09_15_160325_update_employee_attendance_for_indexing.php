<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeAttendanceForIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->index('attendance_date');
            $table->index('employee_type');
            $table->index('clock_in_datetime');
            $table->index('clock_out_datetime');
        });

        Schema::table('employee_attendance_action_logs', function (Blueprint $table) {
            $table->index('employee_type');
            $table->index('action_date');
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
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['employee_type']);
            $table->dropIndex(['clock_in_datetime']);
            $table->dropIndex(['clock_out_datetime']);
        });

        Schema::table('employee_attendance_action_logs', function (Blueprint $table) {
            $table->dropIndex(['employee_type']);
            $table->dropIndex(['action_date']);
        });
    }
}
