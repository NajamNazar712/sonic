<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailParcelReceivingShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_parcel_receiving_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parcel_receiving_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipping_mode_id')->index();
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
        Schema::dropIfExists('retail_parcel_receiving_shipments');
    }
}
