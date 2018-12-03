<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneClassCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_class_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('zone_id');
            $table->integer('city_id');
            $table->integer('class');

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('zone_id');
            $table->index('city_id');
            $table->index('class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zone_class_cities');
    }
}
