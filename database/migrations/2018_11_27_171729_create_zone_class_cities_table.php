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
            $table->integer('city_id');
            $table->integer('zone');

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('zone');
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
