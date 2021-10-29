<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashStatementAndDraftTableWithOperationManagerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statement_details', function ($table) {
            $table->unsignedInteger('operation_manager_id')->index();
        });

        Schema::table('petty_cash_statement_detail_drafts', function ($table) {
            $table->unsignedInteger('operation_manager_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petty_cash_statement_details', function ($table) {
            $table->dropColumn('operation_manager_id');
        });

        Schema::table('petty_cash_statement_detail_drafts', function ($table) {
            $table->dropColumn('operation_manager_id');
        });
    }
}
