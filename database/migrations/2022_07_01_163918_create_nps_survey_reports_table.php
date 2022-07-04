<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpsSurveyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nps_survey_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nps_survey_id')->index();
            $table->integer('user_id')->index();
            $table->integer('promoters');
            $table->integer('passive');
            $table->integer('detractor');
            $table->integer('total_ratting');
            $table->string('recommendations_box',300)->nullable();
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
        Schema::dropIfExists('nps_survey_reports');
    }
}
