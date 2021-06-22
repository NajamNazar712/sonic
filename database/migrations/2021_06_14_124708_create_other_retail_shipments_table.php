<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherRetailShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_retail_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->tinyInteger('parcel_receiving')->dafault(0);
            $table->integer('retail_user_id')->index();
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
        Schema::dropIfExists('other_retail_shipments');
    }
}
