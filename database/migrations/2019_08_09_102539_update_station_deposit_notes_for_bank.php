<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStationDepositNotesForBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->integer('banks_list_id')->nullable()->change();
            $table->bigInteger('sdn_deposit_amount')->nullable();
            $table->tinyInteger('deposit_slip_status')->default(0);
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
            $table->integer('banks_list_id')->change();
            $table->dropColumn('deposit_slip_status');
            $table->dropColumn('sdn_deposit_amount');
        });
    }
}
