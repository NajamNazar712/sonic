<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationsOutgoingPickupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations_outgoing_pickup_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_request_id');
            $table->integer('hub_id');
            $table->integer('booking_type_id');
            $table->integer('shipments_count');
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
        Schema::dropIfExists('operations_outgoing_pickup_requests');
    }
}
