<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalShipmentExtraServiceChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_shipment_extra_service_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->decimal('amount',8,2);
            $table->integer('added_by')->index();
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
        Schema::dropIfExists('international_shipment_extra_service_charges');
    }
}
