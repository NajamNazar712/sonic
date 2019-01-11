<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsPickupJourneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments_pickup_journey', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('shipment_id');
            $table->integer('status_id');
            $table->integer('admin_id')->nullable()->default(NULL);
            $table->integer('reference_1_id')->nullable()->default(NULL);
            $table->integer('reference_2_id')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments_pickup_journey');
    }
}
