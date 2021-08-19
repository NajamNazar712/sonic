<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFnfStatusJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fnf_status_journeys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fnf_id')->index();
            $table->integer('section_id')->index()->nullable();
            $table->integer('status_id')->index();
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
        Schema::dropIfExists('fnf_status_journeys');
    }
}
