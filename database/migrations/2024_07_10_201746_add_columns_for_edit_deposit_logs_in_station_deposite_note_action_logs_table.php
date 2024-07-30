<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForEditDepositLogsInStationDepositeNoteActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposite_note_action_logs', function (Blueprint $table) {
            $table->unsignedInteger('previous_bank_id')->nullable();
            $table->unsignedInteger('new_bank_id')->nullable();
            $table->bigInteger('previous_amount')->nullable();
            $table->bigInteger('new_amount')->nullable();
            $table->string('updated_deposit_slip_image')->nullable();
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
            //
        });
    }
}
