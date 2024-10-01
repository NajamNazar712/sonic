<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddionalChargesTableAddArival extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_additional_charges', function (Blueprint $table) {
            $table->tinyInteger('return_cod_discount_applied')->default(0)->nullable();
            $table->tinyInteger('zero_cod_discount_applied')->default(0)->nullable();
            $table->tinyInteger('arrival_charges_applied')->default(0)->nullable();
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
            $table->dropColumn('return_cod_discount_applied');
            $table->dropColumn('zero_cod_discount_applied');
            $table->dropColumn('arrival_charges_applied');
        });
    }
}
