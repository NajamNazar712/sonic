<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCostCentreToAdvancePettyCashStatementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advance_petty_cash_statement_details', function (Blueprint $table) {
            $table->integer('cost_centre_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advance_petty_cash_statement_details', function (Blueprint $table) {
            $table->dropColumn('cost_centre_id');
        });
    }
}
