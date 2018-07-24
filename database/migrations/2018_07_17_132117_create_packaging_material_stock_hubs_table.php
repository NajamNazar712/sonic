<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagingMaterialStockHubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_material_stock_hubs', function (Blueprint $table) {
            $table->integer('hub_id');
            $table->integer('small_flyers')->default(0);
            $table->integer('medium_flyers')->default(0);
            $table->integer('large_flyers')->default(0);
            $table->integer('boxes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packaging_material_stock_hubs');
    }
}
