<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayFastTransactionDetialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payfast_transaction_detials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_key');
            $table->string('bill_consumer_number');
            $table->string('invoice_number');
            $table->integer('invoice_id');
            $table->string('invoice_ref_id');
            $table->integer('total_amount');
            $table->string('payment_link');
            $table->integer('is_send')->default('0');
            $table->date('due_date');
            $table->date('expiry_date');
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
        Schema::dropIfExists('pay_fast_transaction_detials');
    }
}
