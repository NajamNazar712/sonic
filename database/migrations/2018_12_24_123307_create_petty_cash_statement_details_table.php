<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePettyCashStatementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cash_statement_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('petty_cash_statement_id');
            $table->integer('account_head_id');
            $table->integer('account_title_id');
            $table->integer('hub_id')->nullable();
            $table->timestamp('date');
            $table->string('expense_details');
            $table->double('amount');
            $table->integer('reference_no');
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('petty_cash_statement_details');
    }
}
