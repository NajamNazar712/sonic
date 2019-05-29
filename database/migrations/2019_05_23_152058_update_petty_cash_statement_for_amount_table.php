<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashStatementForAmountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
            $table->double('station_amount')->nullable();
            $table->double('operation_amount')->nullable();
            $table->double('finance_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
            $table->dropColumn('station_amount');
            $table->dropColumn('operation_amount');
            $table->dropColumn('finance_amount');
        });
    }
}
