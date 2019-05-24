<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePettyCashStatementAmountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cash_statement_amount_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petty_cash_statement_detail_id');
            $table->integer('admin_id');
            $table->double('changed_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('petty_cash_statement_amount_logs');
    }
}
