<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailPendingPaymentCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_pending_payment_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('retail_pending_payment_id');
            $table->decimal('amount', 20,2);
            $table->decimal('payable', 20,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retail_pending_payment_calculations');
    }
}
