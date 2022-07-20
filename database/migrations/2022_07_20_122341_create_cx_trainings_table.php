<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCxTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cx_trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id')->index();
            $table->integer('cx_training_unit_id')->index();
            $table->date('joining_date')->nullable();
            $table->date('requested_date');
            $table->integer('requested_by')->index();
            $table->integer('aging');
            $table->integer('training_by')->index();
            $table->integer('status');  // 1 Requested, 2 Inprocess, 3 Completed
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
        Schema::dropIfExists('cx_trainings');
    }
}
