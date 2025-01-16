<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnsShipmentAdditionalCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_additional_charges', function (Blueprint $table) {
            $table->integer('apollo_shipment_id')->nullable();
            $table->tinyInteger('apollo_is_piece')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_additional_charges', function (Blueprint $table) {
            $table->dropColumn('apollo_shipment_id');
            $table->dropColumn('apollo_is_piece');
        });
    }
}
