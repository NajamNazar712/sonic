<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVigilanceVerifiedShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vigilance_verified_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vigilance_verification_id')->index();
            $table->integer('shipment_id')->index();
            $table->tinyInteger('verification_type')->index();
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
        Schema::dropIfExists('vigilance_verified_shipments');
    }
}
