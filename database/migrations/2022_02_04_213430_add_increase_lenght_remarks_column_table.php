<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIncreaseLenghtRemarksColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
            //
            Schema::table('petty_cash_statement_details', function (Blueprint $table) {
                $table->longText('remarks')->change();
            });
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
            //
        });
    }
}
