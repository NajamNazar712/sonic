<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvancePettyCashStatementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_petty_cash_statement_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advance_petty_cash_statement_id')->index();
            $table->integer('status')->index();
            $table->integer('updated_by')->index();
            $table->timestamp('date')->index();
            $table->string('expense_details');
            $table->double('amount');
            $table->integer('reference_no');
            $table->longText('remarks');
            $table->double('station_amount');
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
        Schema::dropIfExists('advance_petty_cash_statement_details');
    }
}
