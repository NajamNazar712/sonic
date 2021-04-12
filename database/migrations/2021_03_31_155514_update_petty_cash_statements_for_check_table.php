<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashStatementsForCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statements', function (Blueprint $table) {
            $table->timestamp('checked_at')->nullable();
            $table->integer('checked_by')->nullable()->index();
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
            $table->dropColumn('checked_at');
            $table->dropColumn('checked_by');
        });
    }
}
