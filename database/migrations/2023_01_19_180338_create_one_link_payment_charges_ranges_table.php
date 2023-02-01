<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOneLinkPaymentChargesRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_link_payment_charges_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('range_up');
            $table->integer('range_down');
            $table->integer('charges');

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
        Schema::dropIfExists('one_link_payment_charges_ranges');
    }
}
