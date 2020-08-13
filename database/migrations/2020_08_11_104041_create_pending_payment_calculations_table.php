<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingPaymentCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_payment_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pending_payment_id');
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
        Schema::dropIfExists('pending_payment_calculations');
    }
}
