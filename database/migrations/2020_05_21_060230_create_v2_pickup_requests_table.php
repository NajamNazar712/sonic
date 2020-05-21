<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2PickupRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_pickup_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('pickup_address_id');
            $table->integer('bookings');
            $table->integer('received')->default(0);
            $table->integer('total_estimated_weight');
            $table->integer('pickup_type');
            $table->integer('status')->default(0);
            $table->integer('reason')->nullable();
            $table->integer('assigned_rider')->nullable();
            $table->integer('not_attempted_count')->default(0);
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
        Schema::dropIfExists('v2_pickup_requests');
    }
}
