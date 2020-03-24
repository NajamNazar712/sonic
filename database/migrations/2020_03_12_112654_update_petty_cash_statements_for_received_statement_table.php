<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashStatementsForReceivedStatementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statements', function (Blueprint $table) {
            $table->integer('finance_received_statement_by')->nullable();
            $table->timestamp('finance_received_statement_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petty_cash_statements', function (Blueprint $table) {
            $table->dropColumn('finance_received_statement_by');
            $table->dropColumn('finance_received_statement_at');
        });
    }
}
