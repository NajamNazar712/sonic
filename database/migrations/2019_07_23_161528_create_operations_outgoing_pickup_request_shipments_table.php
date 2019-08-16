<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationsOutgoingPickupRequestShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations_outgoing_pickup_request_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_outgoing_forecast_id');
            $table->integer('hub_id');
            $table->integer('booking_type_id');
            $table->integer('weight_range_id');
            $table->integer('shipment_id');
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
        Schema::dropIfExists('operations_outgoing_pickup_request_shipments');
    }
}
