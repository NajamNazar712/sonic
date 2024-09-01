<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCorporateRateStatusAddZeroCodAndReturnDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        Schema::table('pending_corporate_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        Schema::table('history_corporate_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        //cod def

        Schema::table('corporate_default_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        Schema::table('pending_corporate_default_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        Schema::table('corporate_default_history_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        //rate status
        Schema::table('rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->boolean('zero_cod_discount')->default(0);
            $table->boolean('return_discount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
        Schema::table('pending_corporate_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
        Schema::table('history_corporate_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });

        Schema::table('corporate_default_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
        Schema::table('pending_corporate_default_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
        Schema::table('corporate_default_history_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });

        Schema::table('rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('zero_cod_discount');
            $table->dropColumn('return_discount');
        });
    }
}
