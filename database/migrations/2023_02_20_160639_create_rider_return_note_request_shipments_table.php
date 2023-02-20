<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderReturnNoteRequestShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_return_note_request_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_note_id')->index();
            $table->integer('shipment_id')->index();
            $table->tinyInteger('open_box')->index();
            $table->integer('ordering')->nullable();
            $table->integer('status')->default(0)->index();
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
        Schema::dropIfExists('rider_return_note_request_shipments');
    }
}
