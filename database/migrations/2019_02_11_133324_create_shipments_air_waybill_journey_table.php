<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsAirWaybillJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments_air_waybill_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('shipment_id');
            $table->integer('user_type');
            $table->integer('user_id');

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments_air_waybill_journey');
    }
}
