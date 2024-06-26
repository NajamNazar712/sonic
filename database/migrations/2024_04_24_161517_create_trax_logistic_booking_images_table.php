<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxLogisticBookingImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_logistic_booking_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id')->index();   
            $table->string('image_name');
            $table->string('image_path');         
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
        Schema::dropIfExists('trax_logistic_booking_images');
    }
}
