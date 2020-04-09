<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentConsigneeLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_consignee_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('previous_location_id')->nullable()->default(NULL);
            $table->integer('current_location_id')->nullable()->default(NULL);
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
        Schema::dropIfExists('shipment_consignee_locations');
    }
}
