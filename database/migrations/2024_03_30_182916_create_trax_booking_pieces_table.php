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
            $table->integer('booking_id');
            $table->integer('piece_cn_number');
//            $table->integer('from_pieces')->nullable();
//            $table->integer('to_pieces')->nullable();
            $table->integer('scan_rider_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->tinyInteger('user_type')->nullable(); // 1 - Admin, 2 - Rider, 0 -> shipper
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('trax_booking_pieces');
    }
}
