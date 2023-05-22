<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateNotificationReturnedDeliveredToShipperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_returned_delivered_to_shipper', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('return_note_id');
            $table->integer('shipment_count');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('notification_returned_delivered_to_shipper');
    }
}
