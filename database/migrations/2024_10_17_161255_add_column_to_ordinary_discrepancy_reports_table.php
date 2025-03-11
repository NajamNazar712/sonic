<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOrdinaryDiscrepancyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordinary_discrepancy_reports', function (Blueprint $table) {
            $table->integer('odr_nature_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordinary_discrepancy_reports', function (Blueprint $table) {
            $table->dropColumn('odr_nature_id');
        });
    }
}
