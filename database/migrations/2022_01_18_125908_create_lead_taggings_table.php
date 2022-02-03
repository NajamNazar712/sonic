<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadTaggingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_taggings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_person_id')->index();
            $table->integer('zone_id')->index();
            $table->integer('city_id')->index();
            $table->integer('service_id')->index();
            $table->integer('count');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        //1 for active 0 for in-active 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_taggings');
    }
}
