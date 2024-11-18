<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExcessHandoverShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('excess_handover_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('handover_id')->nullable();
            $table->unsignedBigInteger('bag_number')->nullable();
            $table->unsignedBigInteger('shipment_ids')->nullable();
            $table->tinyInteger('excess_shipment')->nullable();
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
        Schema::dropIfExists('excess_handover_shipments');
    }
}
