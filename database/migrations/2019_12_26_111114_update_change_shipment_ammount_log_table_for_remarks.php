<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateChangeShipmentAmmountLogTableForRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_shipment_amount_logs', function (Blueprint $table) {
            $table->string('remarks', 250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_shipment_amount_logs', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
}
