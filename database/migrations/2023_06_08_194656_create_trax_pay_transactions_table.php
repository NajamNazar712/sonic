<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxPayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_pay_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->String('link')->nullable();
            $table->bigInteger('shipment_id')->index();
            $table->Integer('delivery_note_id')->index();
            $table->String('unique_code');
            $table->Integer('payment_name_id');
            $table->Integer('cod_amount')->nullable();
            $table->Integer('fintech_amount')->nullable();
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
        Schema::dropIfExists('trax_pay_transactions');
    }
}
