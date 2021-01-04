<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateStandardWeightChargeZoneWisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_standard_weight_charge_zone_wises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->integer('delivery_type_id');
            $table->float('range_up', 8, 2);
            $table->float('range_down', 8, 2);
            $table->integer('local');
            $table->string('same_zone');
            $table->string('different_zone');
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
        Schema::dropIfExists('corporate_standard_weight_charge_zone_wises');
    }
}
