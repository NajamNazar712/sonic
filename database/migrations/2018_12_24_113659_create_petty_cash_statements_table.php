<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePettyCashStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cash_statements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->bigInteger('reference_no')->unique();
            $table->timestamp('from');
            $table->timestamp('to');
            $table->integer('created_by');
            $table->integer('station_approved_by')->nullable();
            $table->timestamp('station_approved_at')->nullable();
            $table->integer('operation_approved_by')->nullable();
            $table->timestamp('operation_approved_at')->nullable();
            $table->integer('finance_approved_by')->nullable();
            $table->timestamp('finance_approved_at')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('petty_cash_statements');
    }
}
