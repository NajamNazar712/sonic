<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashAndDetailAndDraftAndDraftDetailTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statements', function ($table) {
            $table->timestamp('date')->nullable();
            $table->integer('zone_id')->index();
            $table->integer('station_manager_id')->index();
        });

        Schema::table('petty_cash_statement_drafts', function ($table) {
            $table->timestamp('date')->nullable();
            $table->integer('zone_id')->index();
            $table->integer('station_manager_id')->index();
        });

        Schema::table('petty_cash_statement_details', function ($table) {
            $table->integer('dncc_id')->nullable()->index();
            $table->integer('delivered_shipments')->nullable();
        });

        Schema::table('petty_cash_statement_detail_drafts', function ($table) {
            $table->integer('dncc_id')->nullable()->index();
            $table->integer('delivered_shipments')->nullable();
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
