<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalkInInternationalStandardWeightChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_in_international_standard_weight_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->integer('hub_actual_weight');
            $table->integer('hub_chargeable_weight');
            $table->integer('hub_return_charges');
            $table->integer('door_actual_weight');
            $table->integer('door_chargeable_weight');
            $table->integer('door_return_charges');
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
        Schema::dropIfExists('walk_in_international_standard_weight_charges');
    }
}
