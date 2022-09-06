<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOneLinkPaymentTransactionsColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('one_link_payment_transactions');

        Schema::create('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('consumer_number')->index();
            $table->integer('transaction_authentication_id')->index('transaction_authentication_id_index');
            $table->integer('transaction_amount');
            $table->string('transaction_date');
            $table->string('transaction_time');
            $table->string('bank_mnemonic');
            $table->string('reserved')->nullable()->default(NULL);
            $table->integer('consumer_prefix');
            $table->bigInteger('tracking_number')->index();
            $table->integer('shipment_id')->index();
            $table->integer('delivery_note_id')->index();
            $table->tinyInteger('status')->default(1)->index();
            $table->string('error')->nullable()->default(NULL);
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
