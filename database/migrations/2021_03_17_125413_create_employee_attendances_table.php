<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->date('attendance_date');
            $table->Time('clock_in')->nullable();
            $table->Time('clock_out')->nullable();
            $table->string('clock_in_latitude');
            $table->string('clock_in_longitude');
            $table->string('clock_out_latitude');
            $table->string('clock_out_longitude');
            /* 1 -> Admin
               2 -> Rider */
            $table->smallInteger('employee_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_attendances');
    }
}
