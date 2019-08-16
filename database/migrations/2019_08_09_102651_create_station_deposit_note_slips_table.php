<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationDepositNoteSlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_deposit_note_slips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('station_deposit_note_id');
            $table->timestamp('deposit_date');
            $table->integer('bank_id');
            $table->bigInteger('amount');
            $table->string('image');
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
        Schema::dropIfExists('station_deposit_note_slips');
    }
}
