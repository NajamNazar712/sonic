<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStationDepositNotesForPettyCashAdjustment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->integer('petty_cash_statement_id')->nullable();
            $table->string('petty_cash_statement_detail_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->dropColumn('petty_cash_statement_id');
            $table->dropColumn('petty_cash_statement_detail_ids');
        });
    }
}
