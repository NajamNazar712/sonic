<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxBookingPiecesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_booking_pieces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id')->nullable();
            $table->integer('from_pieces')->nullable();
            $table->integer('to_pieces')->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('trax_booking_pieces');
    }
}
