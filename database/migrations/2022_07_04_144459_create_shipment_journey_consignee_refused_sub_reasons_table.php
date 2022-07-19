<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentJourneyConsigneeRefusedSubReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_journey_consignee_refused_sub_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_journey_id')->index();
            $table->integer('status_sub_reason_id');
            $table->integer('status')->default(1);
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('shipment_journey_consignee_refused_sub_reasons');
    }
}
