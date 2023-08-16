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
            $table->integer('rider_wise_delivery_note_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipper_status_id')->index();
            $table->dateTime('delivery_note_date')->index();
            $table->dateTime('updated_date')->index();
            $table->integer('via')->index();

            $table->integer('before_11_count');
            $table->integer('at_11_count');
            $table->integer('at_12_count');
            $table->integer('at_13_count');
            $table->integer('at_14_count');
            $table->integer('at_15_count');
            $table->integer('at_16_count');


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
