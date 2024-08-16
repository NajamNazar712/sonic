<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipmentIdInRetailDiscountCodesAndRetailDiscountAmountInRetailShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_discount_codes', function (Blueprint $table) {
            $table->integer('shipment_id')->nullable();
        });

        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->decimal('retail_discount_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_discount_codes', function (Blueprint $table) {
            $table->dropColumn('shipment_id');
        });

        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->dropColumn('retail_discount_amount');
        });
    }
}
