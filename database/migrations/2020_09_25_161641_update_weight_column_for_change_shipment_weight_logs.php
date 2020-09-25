<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWeightColumnForChangeShipmentWeightLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_shipment_weight_logs', function (Blueprint $table) {
            $table->float('old_charges')->nullable();
            $table->float('new_charges')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_shipment_weight_logs', function (Blueprint $table) {
            $table->dropColumn('old_charges');
            $table->dropColumn('new_charges');
        });
    }
}
