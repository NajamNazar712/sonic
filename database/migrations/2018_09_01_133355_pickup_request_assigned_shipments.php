<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PickupRequestAssignedShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_request_assigned_shipments', function (Blueprint $table) {
            $table->integer('pickup_request_id');
            $table->integer('shipment_id');
            $table->tinyinteger('status')->default(0);
            $table->primary(['pickup_request_id', 'shipment_id'], 'pickup_request_shipment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_request_assigned_shipments');
    }
}
