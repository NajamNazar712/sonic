<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingDestinationMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('booking_destination_mappings', function (Blueprint $table) {
            $table->increments('id');
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
        Schema::dropIfExists('booking_destination_mappings');
    }

}
