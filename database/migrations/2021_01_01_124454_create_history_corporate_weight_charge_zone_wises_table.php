<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryCorporateWeightChargeZoneWisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_corporate_weight_charge_zone_wises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->integer('delivery_type_id');
            $table->float('range_up', 8, 2);
            $table->float('range_down', 8, 2);
            $table->tinyInteger('base')->default(0);
            $table->integer('local');
            $table->string('same_zone');
            $table->string('different_zone');
            $table->timestamps();
            $table->index(['user_id', 'shipping_mode_id', 'delivery_type_id'],'index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_corporate_weight_charge_zone_wises');
    }
}
