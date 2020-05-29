<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2PickupRequestAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_pickup_request_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_request_id');
            $table->integer('rider_id');
            $table->integer('shipments_picked')->nullable();
            $table->integer('shipments_received')->nullable();
            $table->string('reason_id')->nullable();
            $table->string('trax_remarks')->nullable();
            $table->string('shipper_remarks')->nullable();
            $table->timestamp('attempt_date');
            $table->integer('assigned_by');
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
        Schema::dropIfExists('v2_pickup_request_attempts');
    }
}
