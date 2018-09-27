<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('city_booking_types');

        Schema::dropIfExists('city_hubs');

        Schema::dropIfExists('city_infos');

        Schema::dropIfExists('city_pickup');

        Schema::dropIfExists('hub_infos');

        Schema::dropIfExists('pickup_types');

        Schema::dropIfExists('product_user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('city_booking_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('city_hubs', function (Blueprint $table) {
            $table->integer('city_id');
            $table->integer('hub_id');
        });

        Schema::create('city_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city_name');
            $table->integer('city_code')->unique();
            $table->integer('hub_info_id');
        });

        Schema::create('city_pickup', function (Blueprint $table) {
            $table->integer('city_code');
            $table->integer('pickup_type_id');
        });

        Schema::create('hub_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hub_name');
            $table->timestamps();
        });

        Schema::create('pickup_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pickup');
        });

        Schema::create('product_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('product_id');
            $table->timestamps();
        });
    }
}
