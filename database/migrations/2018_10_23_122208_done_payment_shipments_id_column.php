<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DonePaymentShipmentsIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->dropPrimary(['done_payment_id', 'shipment_id', 'type']);
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->increments('id')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->primary(['done_payment_id', 'shipment_id', 'type'], 'primary_index');
        });
    }
}
