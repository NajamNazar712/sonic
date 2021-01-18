<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCashDepositForShippingModeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_cash_deposits', function (Blueprint $table) {
            $table->dropColumn('shipping_mode_id');
        });

        Schema::table('retail_cash_deposit_shipments', function (Blueprint $table) {
            $table->integer('shipping_mode_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_cash_deposits', function (Blueprint $table) {
            $table->integer('shipping_mode_id');
        });

        Schema::table('retail_cash_deposit_shipments', function (Blueprint $table) {
            $table->dropColumn('shipping_mode_id');
        });
    }
}
