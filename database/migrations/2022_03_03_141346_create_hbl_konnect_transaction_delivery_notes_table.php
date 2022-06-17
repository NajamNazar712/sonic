<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHblKonnectTransactionDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hbl_konnect_transaction_delivery_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_note_id')->index();
            $table->bigInteger('transactions_amount');
            $table->bigInteger('cash_amount');
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
        Schema::dropIfExists('hbl_konnect_transaction_delivery_notes');
    }
}
