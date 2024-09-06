<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePaymentAddTransactionid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_payment_shipments', function (Blueprint $table) {
            $table->string('transaction_id')->nullable();
        });
        Schema::table('pending_invoice_shipments', function (Blueprint $table) {
            $table->string('transaction_id')->nullable();
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
            $table->dropColumn('transaction_id');
        });
        Schema::table('pending_invoice_shipments', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
    }
}
