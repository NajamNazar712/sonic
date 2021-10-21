<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table petty_cash_statements modify total_amount DOUBLE(12,2)');
        DB::statement('alter table petty_cash_statement_drafts modify total_amount DOUBLE(12,2)');
        DB::statement('alter table station_deposit_note_adjustments modify amount DOUBLE(12,2)');
        Schema::table('petty_cash_statements', function ($table) {
            $table->integer('origin_hub_id')->index();
            $table->integer('destination_hub_id')->index();
        });

        Schema::table('petty_cash_statement_drafts', function ($table) {
            $table->integer('origin_hub_id')->index();
            $table->integer('destination_hub_id')->index();
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
