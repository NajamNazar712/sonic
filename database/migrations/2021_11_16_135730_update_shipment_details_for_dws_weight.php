<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentDetailsForDwsWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_details', function (Blueprint $table) {
            $table->tinyInteger('dws_status')->nullable()->index();
            $table->string('dws_image')->nullable();
            $table->string('dense_weight')->nullable();
            $table->string('dimension_l')->nullable();
            $table->string('dimension_w')->nullable();
            $table->string('dimension_h')->nullable();
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
            $table->dropColumn('dws_status');
            $table->dropColumn('dws_image');
        });
    }
}
