<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id')->index();
            $table->integer('city_id')->index();
            $table->integer('designation_id')->index();
            $table->integer('department_id')->index();
            $table->integer('department_head_id')->index();
            $table->text('type');
            $table->integer('submitted_by')->index();
            $table->integer('status_id')->index();
            $table->integer('vacancies')->nullable();
            $table->integer('position_type_id')->index()->nullable();
            $table->integer('salary_from')->nullable();
            $table->integer('salary_to')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('skills')->nullable();
            $table->text('job_description')->nullable();
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
        Schema::dropIfExists('employee_requisitions');
    }
}
