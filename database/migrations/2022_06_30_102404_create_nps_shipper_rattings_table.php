<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpsShipperRattingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nps_shipper_rattings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nps_survey_id')->index();
            $table->integer('user_id')->index();
            $table->integer('question_id')->index();
            $table->integer('ratting');
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
        Schema::dropIfExists('nps_shipper_rattings');
    }
}
