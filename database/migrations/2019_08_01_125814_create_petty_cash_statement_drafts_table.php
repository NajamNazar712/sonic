<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePettyCashStatementDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cash_statement_drafts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->string('reference_no')->unique();
            $table->timestamp('from');
            $table->timestamp('to');
            $table->float('total_amount');
            $table->integer('created_by');
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
        Schema::dropIfExists('petty_cash_statement_drafts');
    }
}
