<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->integer('pickups')->index();
            $table->tinyInteger('status')->index();
            $table->integer('shipments')->default(0);
            $table->integer('arrived_shipments')->default(0);
            $table->integer('shipments_scanned_by_rider')->default(0);
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
        Schema::dropIfExists('v3_pickup_notes');
    }
}
