<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelenorShipmentStatusEstimatedTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telenor_shipment_status_estimated_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_status_id');
            $table->integer('eta');
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
        Schema::dropIfExists('telenor_shipment_status_estimated_times');
    }
}
