<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogColumnsInStationDepositeNoteActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposite_note_action_logs', function (Blueprint $table) {
            $table->integer('dncc_id')->nullable();
            $table->bigInteger('dncc_amount')->nullable();
            $table->string('action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_deposite_note_action_logs', function (Blueprint $table) {
            $table->dropColumn('dncc_id');
            $table->dropColumn('dncc_amount');
            $table->dropColumn('action');
        });
    }
}
