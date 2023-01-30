<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailShipmentsForDiscountCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->decimal('charges_with_discount',8,2);
            $table->decimal('discount',8,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_shipments', function (Blueprint $table) {
            $table->dropColumn('charges_with_discount');
            $table->dropColumn('discount');
        });
    }
}
