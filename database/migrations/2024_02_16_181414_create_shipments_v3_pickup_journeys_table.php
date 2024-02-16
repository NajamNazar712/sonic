<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsV3PickupJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments_v3_pickup_journeys', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('shipment_id');
            $table->integer('status_id');
            $table->integer('admin_id')->nullable()->default(NULL);
            $table->integer('reference_1_id')->nullable();
            $table->integer('reference_2_id')->nullable();
            $table->integer('reason_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments_v3_pickup_journeys');
    }
}
