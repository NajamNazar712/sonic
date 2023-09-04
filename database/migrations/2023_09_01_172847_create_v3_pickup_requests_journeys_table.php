<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestsJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_requests_journeys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_request_id')->index();
            $table->integer('status')->index();
            $table->tinyInteger('type')->index(); // 1 - Admin, 2 - Rider, 0 -> shipper
            $table->integer('status_by')->index();
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
        Schema::dropIfExists('v3_pickup_requests_journeys');
    }
}
