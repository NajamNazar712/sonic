<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePendingAndDonePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->float('wht')->default(0);
        });

        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->float('wht')->default(0);
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->float('wht')->default(0);
        });

        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->float('wht')->default(0);
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
            $table->dropColumn('wht');
        });

        Schema::table('pending_payment_calculations', function (Blueprint $table) {
            $table->dropColumn('wht');
        });

        Schema::table('done_payment_shipments', function (Blueprint $table) {
            $table->dropColumn('wht');
        });

        Schema::table('done_payment_calculations', function (Blueprint $table) {
            $table->dropColumn('wht');
        });
    }
}
