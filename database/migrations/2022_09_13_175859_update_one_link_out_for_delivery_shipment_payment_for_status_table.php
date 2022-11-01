<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOneLinkOutForDeliveryShipmentPaymentForStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
            $table->dropIndex('status_index');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
            $table->integer('status')->index();
        });
    }
}
