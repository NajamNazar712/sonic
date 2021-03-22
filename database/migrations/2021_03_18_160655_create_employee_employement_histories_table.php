<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeEmployementHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_employement_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->string('name');
            $table->string('designation');
            $table->timestamp('from');
            $table->timestamp('to');
            $table->string('reason');
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
        Schema::dropIfExists('employee_employement_histories');
    }
}
