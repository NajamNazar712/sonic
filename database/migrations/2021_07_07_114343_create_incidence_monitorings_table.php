<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidenceMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidence_monitorings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('station_id')->index();
            $table->integer('area_id')->index();
            $table->time('time_from');
            $table->time('time_to');
            $table->integer('case_nature_id')->index();
            $table->string('observation');
            $table->integer('nc_level_id')->index();
            $table->timestamp('tagging_date');
            $table->integer('status_id')->default(1)->index();
            $table->string('clip_link');
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
        Schema::dropIfExists('incidence_monitorings');
    }
}
