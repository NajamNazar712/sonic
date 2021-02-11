<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderReturnDeliveryActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_return_delivery_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->timestamp('logged_at');
            $table->integer('type_id')->index();
            $table->integer('return_note_id')->index();
            $table->integer('shipment_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rider_return_delivery_action_logs');
    }
}
