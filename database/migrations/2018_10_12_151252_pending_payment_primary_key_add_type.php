<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PendingPaymentPrimaryKeyAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->dropPrimary(['pending_payment_id', 'shipment_id']);

            $table->primary(['pending_payment_id', 'shipment_id', 'type'], 'primary_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->dropPrimary(['pending_payment_id', 'shipment_id', 'type']);

            $table->primary(['pending_payment_id', 'shipment_id']);
        });
    }
}
