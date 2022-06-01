<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReattemptShipmentStatusRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reattempt_shipment_status_remarks', function (Blueprint $table) {
            //for status column automatic or manual
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->string('remarks');
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
        Schema::dropIfExists('reattempt_shipment_status_remarks');
    }
}
