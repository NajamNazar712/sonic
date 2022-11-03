<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOneLinkOutForDeliveryShipmentPaymentsAddId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
            $table->Integer('id')->nullable()->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
}
