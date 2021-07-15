<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentDistributionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_distribution_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->integer('product_type_id')->index();
            $table->integer('items');
            $table->integer('units_per_item');
            $table->integer('price');
            $table->boolean('insurance');
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
        Schema::dropIfExists('shipment_distribution_products');
    }
}
