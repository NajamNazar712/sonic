<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RetailDonePaymentShipmentsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_done_payment_shipments', function (Blueprint $table) {
            $table->index('retail_done_payment_id');
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
        Schema::table('retail_done_payment_shipments', function (Blueprint $table) {
            $table->dropIndex(['retail_done_payment_id']);
            $table->dropIndex(['type']);
        });
    }
}
