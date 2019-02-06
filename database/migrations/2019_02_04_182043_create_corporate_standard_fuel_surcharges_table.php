<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateStandardFuelSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_standard_fuel_surcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->float('fuel_surcharge',10,2);
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
        Schema::dropIfExists('corporate_standard_fuel_surcharges');
    }
}
