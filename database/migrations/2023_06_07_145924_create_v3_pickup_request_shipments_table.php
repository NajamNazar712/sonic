<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_request_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_request_id');
            $table->integer('shipment_id');
            $table->tinyInteger('status')->default(0);
            $table->integer('rider_id')->nullable()->index();
            $table->timestamps();
            $table->index(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('v3_pickup_request_shipments');
    }
}
