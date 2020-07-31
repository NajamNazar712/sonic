<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFulfillmentChargesToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('packaging_charges', function (Blueprint $table) {
            $table->decimal('charges')->change();
        });
        Schema::table('wms_labelling_charges', function (Blueprint $table) {
            $table->decimal('charges')->change();
        });
        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->decimal('charges')->change();
        });
        Schema::table('wms_pending_labelling_charges', function (Blueprint $table) {
            $table->decimal('charges')->change();
        });
        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->decimal('charges')->change();
        });
        Schema::table('wms_history_labelling_charges', function (Blueprint $table) {
            $table->decimal('charges')->change();
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
            $table->integer('charges')->change();
        });
        Schema::table('pending_packaging_charges', function (Blueprint $table) {
            $table->integer('charges')->change();
        });
        Schema::table('history_packaging_charges', function (Blueprint $table) {
            $table->integer('charges')->change();
        });
    }
}
