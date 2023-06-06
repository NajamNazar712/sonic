<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipementReceiveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_receiver_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('shipment_id')->index();
            $table->bigInteger('tracking_number')->index();
            $table->String('receiver_name');
            $table->bigInteger('receiver_cnic');
            $table->String('receiver_relationship');
            $table->Integer('received_by');
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
        Schema::dropIfExists('shipement_receive_details');
    }
}
