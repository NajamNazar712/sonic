<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryLocationMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_location_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area_name');
            $table->integer('city_id')->index();
            $table->integer('added_by')->index();
            $table->integer('updated_by')->index()->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('delivery_location_mappings');
    }
}
