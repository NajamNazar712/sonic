<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashForRejection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statements', function (Blueprint $table) {
            $table->integer('rejected_by')->nullable();
            $table->integer('rejected_at')->nullable();
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
            $table->dropColumn('rejected_by');
            $table->dropColumn('rejected_at');
        });
    }
}
