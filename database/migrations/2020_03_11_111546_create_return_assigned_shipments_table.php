<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnAssignedShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_assigned_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id');
            $table->integer('shipment_id');
            $table->integer('status');
            $table->integer('assigned_by');
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
        Schema::dropIfExists('return_assigned_shipments');
    }
}
