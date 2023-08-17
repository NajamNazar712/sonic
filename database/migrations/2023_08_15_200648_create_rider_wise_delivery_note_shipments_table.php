<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderWiseDeliveryNoteShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_wise_delivery_note_shipments', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('rwdnsum_id')->index();
            $table->integer('rwdn_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipper_status_id')->index();
            $table->dateTime('updated_time')->index();
            $table->integer('updated_via')->index()->nullable();

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
        Schema::dropIfExists('rider_wise_delivery_note_shipments');
    }
}
