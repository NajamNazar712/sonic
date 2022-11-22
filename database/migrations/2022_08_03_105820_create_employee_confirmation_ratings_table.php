<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeConfirmationRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_confirmation_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_confirmation_id')->index();
            $table->smallInteger('job');
            $table->text('job_comments')->nullable();
            $table->smallInteger('quality');
            $table->text('quality_comments')->nullable();
            $table->smallInteger('attendance');
            $table->text('attendance_comments')->nullable();
            $table->smallInteger('initiative');
            $table->text('initiative_comments')->nullable();
            $table->smallInteger('communication');
            $table->text('communication_comments')->nullable();
            $table->smallInteger('dependability');
            $table->text('dependability_comments')->nullable();
            $table->decimal('overall_rating',8,2);
            $table->text('evaluation_comments')->nullable();
            $table->integer('added_by')->index();
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
        Schema::dropIfExists('employee_confirmation_ratings');
    }
}
