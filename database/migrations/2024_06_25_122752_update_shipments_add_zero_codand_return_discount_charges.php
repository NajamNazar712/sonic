<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsAddZeroCodandReturnDiscountCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->tinyInteger('return_cod_discount_applied')->default(0);
            $table->tinyInteger('zero_cod_discount_applied')->default(0);
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
            $table->dropColumn('return_cod_discount_applied');
            $table->dropColumn('zero_cod_discount_applied');
        });
    }
}
