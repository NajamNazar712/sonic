<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsTableForParcelValueAndFintechCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->integer('parcel_value')->nullable();
            $table->integer('fintech_charges')->nullable();
        });

        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->integer('piece_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('parcel_value');
            $table->dropColumn('fintech_charges');
        });

        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->dropColumn('piece_id');
        });
    }
}
