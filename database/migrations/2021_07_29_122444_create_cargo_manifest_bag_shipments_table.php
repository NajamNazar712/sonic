<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoManifestBagShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_manifest_bag_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cargo_manifest_bag_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('status')->index();
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
        Schema::dropIfExists('cargo_manifest_bag_shipments');
    }
}
