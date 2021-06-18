<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherParcelReceivingShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_parcel_receiving_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('other_parcel_receiving_id')->index();
            $table->integer('shipment_id')->index();
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
        Schema::dropIfExists('other_parcel_receiving_shipments');
    }
}
