<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangeShipmentAmountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_shipment_amount_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->bigInteger('old_amount');
            $table->bigInteger('new_amount');
            $table->integer('admin_id');
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
        Schema::dropIfExists('change_shipment_amount_logs');
    }
}
