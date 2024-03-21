<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationDepositeNoteActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_deposite_note_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sdn_id')->index();
            $table->integer('admin_id')->index();
            $table->integer('status_id')->index();
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
        Schema::dropIfExists('station_deposite_note_action_logs');
    }
}
