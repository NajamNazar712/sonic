<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashStatementDetailsTableForEdit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
            $table->string('edit_by')->nullable()->index();
            $table->timestamp('edit_at')->nullable()->index();
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
            $table->dropColumn('edit_by');
            $table->dropColumn('edit_at');
        });
    }
}
