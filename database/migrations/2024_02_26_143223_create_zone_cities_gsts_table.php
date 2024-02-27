<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoneCitiesGstsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_cities_gsts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('zone_id')->index();
            $table->integer('city_id')->index();
            $table->decimal('gst')->index();
            $table->integer('status')->default(0);
            $table->integer('updated_by')->index();
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
        Schema::dropIfExists('zone_cities_gsts');
    }
}
