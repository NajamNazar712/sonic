<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderWiseDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_wise_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
//            $table->integer('shipment_id')->index();
            $table->integer('delivery_note_id')->index();
            $table->integer('rider_id')->index();
            $table->integer('trax_id')->index();
            $table->integer('rider_name');
            $table->integer('hub_id')->index();
            $table->string('hub_name');
            $table->string('zone_name');
            $table->integer('count');
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
        Schema::dropIfExists('rider_wise_delivery_notes');
    }
}
