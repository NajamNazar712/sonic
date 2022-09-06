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
        Schema::create('one_link_out_for_delivery_shipment_payments', function (Blueprint $table) {
            $table->bigInteger('consumer_number')->index('consumer_number_index');
            $table->integer('transaction_authentication_id')->index('transaction_authentication_id_index');
            $table->decimal('transaction_amount');
            $table->string('transaction_date');
            $table->string('transaction_time');
            $table->string('bank_mnemonic');
            $table->string('reserved')->nullable()->default(NULL);
            $table->integer('consumer_prefix');
            $table->bigInteger('tracking_number')->index('tracking_number_index');
            $table->integer('shipment_id')->index('shipment_id_index');
            $table->integer('delivery_note_id')->index('delivery_note_id_index');
            $table->tinyInteger('status')->default(1)->index('status_index');
            $table->string('error')->nullable()->default(NULL);
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
        Schema::dropIfExists('one_link_out_for_delivery_shipment_payments');
    }
}
