<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddImageStationDepositNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('station_deposit_notes', function (Blueprint $table) {
            $table->string('deposit_slip')->after('banks_list_id')->nullable();
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
           $table->dropColumn('deposit_slip');
        });
    }
}
