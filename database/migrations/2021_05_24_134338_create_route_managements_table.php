<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_managements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('route_code');
            $table->string('route_title');
            $table->integer('starting_point_id');
            $table->integer('end_point_id');
            $table->integer('junction_id');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('route_managements');
    }
}
