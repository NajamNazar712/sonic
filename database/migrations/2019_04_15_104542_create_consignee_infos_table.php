<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsigneeInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignee_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('city_id');
            $table->string('name');
            $table->string('address');
            $table->string('phone_number_1');
            $table->string('phone_number_2')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('consignee_infos');
    }
}
