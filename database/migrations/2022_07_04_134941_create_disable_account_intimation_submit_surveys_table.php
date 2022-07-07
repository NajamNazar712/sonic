<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisableAccountIntimationSubmitSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disable_account_intimation_submit_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('survey_id')->index();
            $table->integer('question_id')->index();
            $table->string('selected_option')->nullable();
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
        Schema::dropIfExists('disable_account_intimation_submit_surveys');
    }
}
