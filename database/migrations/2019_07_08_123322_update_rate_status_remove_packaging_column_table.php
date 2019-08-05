<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRateStatusRemovePackagingColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_statuses', function (Blueprint $table) {
            $table->dropColumn('packaging_charges');
        });

        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('packaging_charges');
        });

        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->dropColumn('packaging_charges');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_statuses', function (Blueprint $table) {
            $table->tinyInteger('packaging_charges');
        });

        Schema::table('pending_rate_statuses', function (Blueprint $table) {
            $table->tinyInteger('packaging_charges');
        });

        Schema::table('history_rate_statuses', function (Blueprint $table) {
            $table->tinyInteger('packaging_charges');
        });
    }
}
