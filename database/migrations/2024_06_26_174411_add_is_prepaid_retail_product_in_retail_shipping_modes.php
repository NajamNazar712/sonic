<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPrepaidRetailProductInRetailShippingModes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipping_modes', function (Blueprint $table) {
            $table->string('is_prepaid_retail_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipping_modes', function (Blueprint $table) {
            $table->dropColumn('is_prepaid_retail_products');
        });
    }
}
