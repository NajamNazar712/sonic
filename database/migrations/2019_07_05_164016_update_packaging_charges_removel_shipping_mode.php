<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackagingChargesRemovelShippingMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->dropColumn('shipping_mode_id');
        });

        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->dropColumn('shipping_mode_id');
        });

        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->dropColumn('shipping_mode_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->integer('shipping_mode_id');
        });

        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->integer('shipping_mode_id');
        });

        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->integer('shipping_mode_id');
        });
    }
}
