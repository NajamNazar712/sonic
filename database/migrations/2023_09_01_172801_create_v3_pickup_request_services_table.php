<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV3PickupRequestServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v3_pickup_request_services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pickup_request_id')->index();
            $table->integer('pickup_request_service_id')->index();
            $table->integer('count');
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
        Schema::dropIfExists('v3_pickup_request_services');
    }
}
