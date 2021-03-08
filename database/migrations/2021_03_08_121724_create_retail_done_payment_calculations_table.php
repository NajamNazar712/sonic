<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailDonePaymentCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_done_payment_calculations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('retail_done_payment_id');
            $table->decimal('amount', 20,2);
            $table->decimal('payable', 20,2);
            $table->decimal('adjustment', 20,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retail_done_payment_calculations');
    }
}
