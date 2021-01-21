<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupNoteStationDepositNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_note_station_deposit_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('station_deposit_note_id')->index();
            $table->integer('retail_pickup_note_id')->index();
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
        Schema::dropIfExists('pickup_note_station_deposit_notes');
    }
}
