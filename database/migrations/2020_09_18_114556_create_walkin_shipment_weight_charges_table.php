<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalkinShipmentWeightChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walkin_shipment_weight_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->decimal('charges_per_kg');
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
        Schema::dropIfExists('walkin_shipment_weight_charges');
    }
}
