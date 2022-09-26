<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderDeliveryNoteRequestShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_delivery_note_request_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_note_id')->index();
            $table->integer('shipment_id')->index();
            $table->tinyInteger('notification')->index();
            $table->tinyInteger('rider_information')->index();
            $table->tinyInteger('open_box')->index();
            $table->integer('ordering')->nullable();
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
        Schema::dropIfExists('rider_delivery_note_request_shipments');
    }
}
