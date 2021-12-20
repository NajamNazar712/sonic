<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentDetailsForDwsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_details', function (Blueprint $table) {
            $table->string('dws_machine')->nullable();
            $table->string('dws_package_type')->nullable();
            $table->string('dws_is_uploaded')->nullable();
            $table->string('dws_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_details', function (Blueprint $table) {
            $table->dropColumn('dws_machine');
            $table->dropColumn('dws_package_type');
            $table->dropColumn('dws_is_uploaded');
            $table->dropColumn('dws_date');
        });
    }
}
