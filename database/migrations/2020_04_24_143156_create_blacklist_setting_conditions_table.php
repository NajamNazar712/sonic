<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistSettingConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklist_setting_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blacklist_setting_id');
            $table->integer('blacklist_condition_id');
            $table->integer('blacklist_logic_id');
            $table->integer('blacklist_logic_value');
            $table->integer('blacklist_shipment_range_id');
            $table->integer('blacklist_shipment_range_value');
            $table->integer('blacklist_operation_id')->nullable();
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
        Schema::dropIfExists('blacklist_setting_conditions');
    }
}
