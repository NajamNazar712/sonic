<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('pickup_address_id');
            $table->integer('booked');
            $table->integer('received')->nullable();
            $table->integer('city_id');
            $table->integer('status_id')->default(1);
            $table->integer('rider_status')->default(1);
            $table->integer('attempts')->default(0);
            $table->integer('current_rider_id')->nullable();
            $table->integer('last_rider_id')->nullable();
            $table->integer('last_updated_by')->nullable();
            $table->tinyInteger('after_cut_off_time')->nullable();
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
        Schema::dropIfExists('v3_pickup_requests');
    }
}
