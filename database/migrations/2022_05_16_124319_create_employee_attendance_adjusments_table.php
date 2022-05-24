<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAttendanceAdjusmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attendance_adjustments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->integer('employee_type_id')->index();
            $table->integer('reporter_id')->index();
            $table->date('date');
            $table->integer('updated_by')->index()->nullable();
            $table->string('applied_reason', 500);
            $table->string('rejected_reason', 500)->nullable();
            $table->integer('status')->default(1)->index();
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
        Schema::dropIfExists('employee_attendance_adjusments');
    }
}
