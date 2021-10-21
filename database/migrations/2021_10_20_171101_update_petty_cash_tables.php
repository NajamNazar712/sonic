<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statement_details', function ($table) {
            $table->integer('employee_id')->nullable()->change();
            $table->string('employee_name')->nullable()->change();
            $table->string('employee_designation')->nullable()->change();
            $table->integer('reference_no')->nullable()->change();
        });

        Schema::table('petty_cash_statement_detail_drafts', function ($table) {
            $table->integer('employee_id')->nullable()->change();
            $table->string('employee_name')->nullable()->change();
            $table->string('employee_designation')->nullable()->change();
            $table->integer('reference_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
