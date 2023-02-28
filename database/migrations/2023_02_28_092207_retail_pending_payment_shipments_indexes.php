<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RetailPendingPaymentShipmentsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_pending_payment_shipments', function (Blueprint $table) {
            $table->index('retail_pending_payment_id');
            $table->index('shipment_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_pending_payment_shipments', function (Blueprint $table) {
            $table->dropIndex(['retail_pending_payment_id']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['type']);
        });
    }
}
