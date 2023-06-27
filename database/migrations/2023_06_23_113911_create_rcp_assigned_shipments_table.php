<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRcpAssignedShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcp_assigned_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rcp_assigned_agent_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipment_status');
            $table->integer('status_remarks_id')->index()->nullable();
            $table->integer('assigned_status');
            $table->integer('assigned_by');
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
        Schema::dropIfExists('rcp_assigned_shipments');
    }
}
