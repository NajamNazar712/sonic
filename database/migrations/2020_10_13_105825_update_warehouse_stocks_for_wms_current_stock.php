<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWarehouseStocksForWmsCurrentStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->integer('wms_product_id');
            $table->integer('wms_current_stock_id')->nullable();
        });
        Schema::table('warehouses', function (Blueprint $table) {
            $table->integer('pickup_address_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->dropColumn('wms_product_id');
            $table->dropColumn('wms_current_stock_id');
        });
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('pickup_address_id');
        });
    }
}
