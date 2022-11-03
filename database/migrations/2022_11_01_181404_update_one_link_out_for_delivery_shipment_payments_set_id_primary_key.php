<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOneLinkOutForDeliveryShipmentPaymentsSetIdPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
//        Schema::table('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
////            $table->primary('id')->unsigned(true)->nullable(false)->change();
//
//        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
//        Schema::table('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
//            $table->Integer('id',false)->change();
//            $table->dropPrimary('id');
//
//
//        });
    }
}
