<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentDistributionProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_distribution_products', function (Blueprint $table) {
            $table->integer('total_delivered_units')->after('units_per_item')->default(0);
            $table->integer('received_amount')->after('price')->default(0);
            $table->integer('total_delivered_skus')->after('received_amount')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_distribution_products', function (Blueprint $table) {
            $table->dropColumn('total_delivered_units');
            $table->dropColumn('received_amount');
            $table->dropColumn('total_delivered_skus');
        });
    }
}
