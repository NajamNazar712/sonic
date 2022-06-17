<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsForFuelFactorSurchargeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('shipments', function (Blueprint $table) {
            $table->Decimal('esc_charges ')->after('fuel_surcharge')->nullable();
            $table->Decimal('open_box_charges ')->after('fuel_surcharge')->nullable();
            $table->Decimal('fuel_factor_surcharge')->after('fuel_surcharge')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('fuel_factor_surcharge');
        });
    }
}
