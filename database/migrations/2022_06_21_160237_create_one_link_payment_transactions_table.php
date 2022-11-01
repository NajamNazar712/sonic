<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOneLinkPaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_link_payment_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('consumer_number')->index();
            $table->integer('tran_auth_id')->index();
            $table->string('transaction_amount');
            $table->string('tran_date');
            $table->string('tran_time');
            $table->string('bank_mnemonic');
            $table->string('reserved')->nullable();
            $table->string('consumer_prefix');
            $table->string('tracking_no')->index();
            $table->integer('shipment_id')->index();
            $table->integer('amount');
            $table->timestamp('tran_date_formated');
            $table->string('tran_time_formated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_link_payment_transactions');
    }
}
