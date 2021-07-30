<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2JunctionRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_junction_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('junction_mapping_id')->index();
            $table->integer('starting_hub_id')->index();
            $table->integer('ending_hub_id')->index();
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
        Schema::dropIfExists('v2_junction_routes');
    }
}
