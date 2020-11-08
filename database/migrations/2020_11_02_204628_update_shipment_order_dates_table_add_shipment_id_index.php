<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentOrderDatesTableAddShipmentIdIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_order_dates', function (Blueprint $table) {
            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_order_dates', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
        });
    }
}
