<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfSectionEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_section_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->integer('line_manager')->index();
            $table->integer('hod')->index();
            $table->date('joining_date');
            $table->date('resign_date');
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index()->nullable();
            $table->integer('status_id')->index();
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
        Schema::dropIfExists('fnf_section_employees');
    }
}
