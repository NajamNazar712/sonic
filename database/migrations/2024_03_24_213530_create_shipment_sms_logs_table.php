<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sms_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('notification_id')->index();
            $table->boolean('paid');
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
        Schema::dropIfExists('shipment_sms_logs');
    }
}
