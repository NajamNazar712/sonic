<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentScanningJourneyAreaLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_scanning_journey_area_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->integer('shipment_scanning_journey_id')->index('idx_shipment_scanning_journey_id');
            $table->integer('admin_id')->index();
            $table->integer('hub_id')->nullable()->index();
            $table->integer('area_id')->nullable()->index();
            $table->integer('location_status');
            $table->integer('status');
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
        Schema::dropIfExists('shipment_scanning_journey_area_logs');
    }
}
