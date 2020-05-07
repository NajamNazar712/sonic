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
            $table->tinyInteger('weight_addition')->after('range_down')->default(0);
            $table->decimal('spkg')->after('weight_addition')->nullable();
        });
        Schema::table('history_corporate_weight_charges', function (Blueprint $table) {
            $table->tinyInteger('weight_addition')->after('range_down')->default(0);
            $table->decimal('spkg')->after('weight_addition')->nullable();
        });
        Schema::table('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->tinyInteger('weight_addition')->after('range_down')->default(0);
            $table->decimal('spkg')->after('weight_addition')->nullable();
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
            $table->dropColumn('weight_addition');
            $table->dropColumn('spkg');
        });
        Schema::table('history_corporate_weight_charges', function (Blueprint $table) {
            $table->dropColumn('weight_addition');
            $table->dropColumn('spkg');
        });
        Schema::table('pending_corporate_weight_charges', function (Blueprint $table) {
            $table->dropColumn('weight_addition');
            $table->dropColumn('spkg');
        });
    }
}
