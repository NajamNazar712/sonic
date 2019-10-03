<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalkInShipmentPackagingMaterialHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_in_shipment_packaging_material_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('type_id');
            $table->integer('size_id');
            $table->integer('quantity');
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
        Schema::dropIfExists('walk_in_shipment_packaging_material_histories');
    }
}
