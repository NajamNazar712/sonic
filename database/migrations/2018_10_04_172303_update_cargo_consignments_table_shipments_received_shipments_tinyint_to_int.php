<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoConsignmentsTableShipmentsReceivedShipmentsTinyintToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->integer('shipments')->change();
            $table->integer('received_shipments')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->tinyInteger('shipments')->change();
            $table->tinyInteger('received_shipments')->change();
        });
    }
}
