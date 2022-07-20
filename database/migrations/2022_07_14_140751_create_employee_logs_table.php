<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id')->index();
            $table->integer('employee_type_id')->index()->nullable(); // rider or staff
            $table->integer('staff_category_id')->index()->nullable(); //
            $table->integer('status_id')->index()->nullable(); // active,inactive n active no info ()
            $table->integer('rider_type_id')->index()->nullable(); // rider incentive (2)/rider perment (1)
            $table->integer('blacklist')->nullable();
            $table->integer('update_pin')->nullable();
            $table->integer('updated_by')->index();
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
        Schema::dropIfExists('employee_logs');
    }
}
