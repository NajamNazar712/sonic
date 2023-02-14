<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailRequestCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_request_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('added_by')->index();
            $table->integer('business_category_id')->index();
            $table->integer('shipping_mode_id')->index();
            $table->string('city_name');
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
        Schema::dropIfExists('retail_request_cities');
    }
}
