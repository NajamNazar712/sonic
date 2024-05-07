<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentStatusScreenLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_status_screen_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('shipment_status_id');
            $table->unsignedBigInteger('screen_location_id');
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
        Schema::dropIfExists('shipment_status_screen_locations');
    }
}
