<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->string('tracking_number');
            $table->string('origin');
            $table->string('destination');
            $table->string('status');
            $table->string('status_at');
            $table->string('status_by');
            $table->string('screen_location');
            $table->string('city');
            $table->string('scanned_by');
            $table->string('scanned_at');
            $table->string('handover_note');
            $table->string('handover_created_by');
            $table->string('handover_created_at');
            $table->string('handover_from');
            $table->string('handover_to');
            $table->string('handover_received_by');
            $table->string('handover_received_at');
            $table->string('last_action');
            $table->integer('tracked_by')->index();
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
        Schema::dropIfExists('shipment_positions');
    }
}
