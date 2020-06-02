<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2RiderPickupActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_rider_pickup_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('logged_at');
            $table->integer('type_id');
            $table->integer('pickup_request_id')->nullable();
            $table->integer('reference_1_id')->nullable();
            $table->integer('reference_2_id')->nullable();
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
        Schema::dropIfExists('v2_rider_pickup_action_logs');
    }
}
