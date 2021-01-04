<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingCorporateReturnChargeZoneWisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_corporate_return_charge_zone_wises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->integer('local');
            $table->string('same_zone');
            $table->string('different_zone');
            $table->timestamps();
            $table->index(['user_id', 'shipping_mode_id'],'index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_corporate_return_charge_zone_wises');
    }
}
