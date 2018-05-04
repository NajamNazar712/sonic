<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateSwitchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_switches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->boolean('sw_overnight')->default(0);
            $table->boolean('sw_overland')->default(0);
            $table->boolean('sw_detain')->default(0);
            $table->boolean('sw_sameday')->default(0);
            $table->boolean('sw_cash_handle')->default(0);
            $table->boolean('sw_insurance')->default(0);
            $table->boolean('sw_return')->default(0);
            $table->boolean('sw_packaging')->default(0);
            $table->boolean('sw_discount_weight')->default(0);
            $table->boolean('sw_discount_cash')->default(0);
            $table->boolean('sw_discount_insurance')->default(0);
            $table->boolean('sw_discount_return')->default(0);
            $table->boolean('sw_discount_packaging')->default(0);
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
        Schema::dropIfExists('rate_switches');
    }
}
