<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpsSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nps_survey', function (Blueprint $table) {
            $table->increments('id');
            $table->string('survey_name');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('recommendation_box')->default(0)->nullable();
            $table->integer('all_shipper')->default(0)->nullable();
            $table->string('shipper_ids',500)->nullable();
            $table->integer('status')->default(0);
            $table->integer('admin_id')->index();
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
        Schema::dropIfExists('nps_survey');
    }
}
