<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationDepositNoteAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_deposit_note_adjustments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sdn_id')->index();
            $table->integer('petty_cash_statement_id')->index();
            $table->timestamp('date');
            $table->float('amount');
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
        Schema::dropIfExists('station_deposit_note_adjustments');
    }
}
