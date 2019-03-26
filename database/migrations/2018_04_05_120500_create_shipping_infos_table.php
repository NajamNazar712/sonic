<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shipping_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('pickup_address');
            //$table->integer('city_id');
            $table->string('poc');
            $table->string('phone');
            $table->string('email');
            $table->integer('city_code');
            $table->timestamps();
            //$table->index(['user_id','city_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_shipping_infos');
    }
}
