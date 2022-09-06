<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderIncentiveDeliveryShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_incentive_delivery_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->date('date');
            $table->integer('courier_type_id')->index();
            $table->integer('shipment_type_id')->index();
            $table->integer('shipment_weight_type_id')->index();
            $table->integer('shipment_id');
            $table->integer('rate');
            $table->integer('incentive');
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
        Schema::dropIfExists('rider_incentive_delivery_shipments');
    }
}
