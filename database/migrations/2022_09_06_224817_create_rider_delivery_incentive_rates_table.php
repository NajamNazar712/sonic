<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderDeliveryIncentiveRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_delivery_incentive_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->nullable()->index();
            $table->integer('courier_type_id')->index();
            $table->integer('shipment_type_id')->index();
            $table->integer('shipment_weight_type_id')->index();
            $table->integer('rate');
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
        Schema::dropIfExists('rider_delivery_incentive_rates');
    }
}
