<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationDepositNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_deposit_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->integer('dncc_count');
            $table->integer('sdn_delivered_shipments');
            $table->bigInteger('sdn_amount');
            $table->bigInteger('sdn_expense');
            $table->bigInteger('sdn_net_amount');
            $table->integer('deposited_by');
            $table->integer('banks_list_id');
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
        Schema::dropIfExists('station_deposit_notes');
    }
}
