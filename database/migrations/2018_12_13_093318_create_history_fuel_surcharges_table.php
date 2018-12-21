<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryFuelSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_fuel_surcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->decimal('fuel_surcharge',10,2);
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
        Schema::dropIfExists('history_fuel_surcharges');
    }
}
