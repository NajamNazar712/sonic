<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandardCashHandlingChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_cash_handling_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->integer('range_up');
            $table->integer('range_down');
            $table->string('charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('standard_cash_handling_charges');
    }
}
