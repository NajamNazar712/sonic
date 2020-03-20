<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessProjectionHubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_projection_hubs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->integer('average_shipment');
            $table->integer('projected_shipment');
            $table->integer('last_day_number');
            $table->integer('achieved');
            $table->date('date');
            $table->timestamps();
            $table->index(['hub_id', 'average_shipment', 'projected_shipment', 'last_day_number', 'achieved', 'date','created_at','updated_at'],'index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_projection_hubs');
    }
}
