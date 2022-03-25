<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderPickupInvalidLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_pickup_invalid_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->integer('pickup_note_id')->index();
            $table->integer('pickup_request_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipment_status_id')->index();
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
        Schema::dropIfExists('rider_pickup_invalid_logs');
    }
}
