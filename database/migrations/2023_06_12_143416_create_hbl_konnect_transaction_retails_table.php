<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHblKonnectTransactionRetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hbl_konnect_transaction_retails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->index();
            $table->integer('retail_note_id')->index();
            $table->bigInteger('amount');
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
        Schema::dropIfExists('hbl_konnect_transaction_retails');
    }
}
