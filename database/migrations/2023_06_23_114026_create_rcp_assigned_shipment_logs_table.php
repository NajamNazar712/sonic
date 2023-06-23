<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRcpAssignedShipmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcp_assigned_shipment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rcp_assigned_shipment_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipment_status');
            $table->integer('admin_id')->index()->nullable();
            $table->integer('user_id')->index()->nullable();
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
        Schema::dropIfExists('rcp_assigned_shipment_logs');
    }
}
