<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandoverShipmentPiecesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handover_shipment_pieces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pieces_id');
            $table->integer('shipment_id')->index();
            $table->integer('tracking_number');
            $table->tinyInteger('receive_status')->default(0);
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
        Schema::dropIfExists('handover_shipment_pieces');
    }
}
