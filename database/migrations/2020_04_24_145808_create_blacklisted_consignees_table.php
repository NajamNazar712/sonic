<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistedConsigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklisted_consignees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignee_information_id');
            $table->integer('shipments');
            $table->integer('delivered');
            $table->string('delivered_ratio');
            $table->integer('undelivered');
            $table->string('undelivered_ratio');
            $table->integer('return');
            $table->string('return_ratio');
            $table->integer('blacklist_setting_id');
            $table->integer('blacklist_condition_id');
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
        Schema::dropIfExists('blacklisted_consignees');
    }
}
