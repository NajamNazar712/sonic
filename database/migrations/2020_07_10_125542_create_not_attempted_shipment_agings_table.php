<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotAttemptedShipmentAgingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('not_attempted_shipment_agings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->integer('zone_id');
            $table->integer('zero');
            $table->integer('one');
            $table->integer('two');
            $table->integer('three');
            $table->integer('four');
            $table->integer('five');
            $table->integer('six_plus');
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
        Schema::dropIfExists('not_attempted_shipment_agings');
    }
}
