<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandardBookingTypeChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_booking_type_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->float('replacement_charges');
            $table->float('try_and_buy_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('standard_booking_type_charges');
    }
}
