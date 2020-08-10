<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonePaymentChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('done_payment_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('done_payment_id');
            $table->decimal('amount');
            $table->decimal('charges');
            $table->decimal('gst');
            $table->decimal('payable');
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
        Schema::dropIfExists('done_payment_charges');
    }
}
