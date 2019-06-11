<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id');
            $table->integer('hub');
            $table->integer('hub_id')->nullable();
            $table->integer('zone_id');
            $table->integer('pickup');
            $table->integer('status');
            $table->integer('updated_by');
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
        Schema::dropIfExists('city_histories');
    }
}
