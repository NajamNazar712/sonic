<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCorporateWeightChargesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_weight_charges', function (Blueprint $table) {
            $table->tinyInteger('base')->after('range_down')->default(0);
        });
        Schema::table('history_corporate_weight_charges', function (Blueprint $table) {
            $table->tinyInteger('base')->after('range_down')->default(0);
        });
        Schema::table('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->tinyInteger('base')->after('range_down')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_weight_charges', function (Blueprint $table) {
            $table->dropColumn('base');
        });
        Schema::table('history_corporate_weight_charges', function (Blueprint $table) {
            $table->dropColumn('base');
        });
        Schema::table('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->dropColumn('base');
        });
    }
}
