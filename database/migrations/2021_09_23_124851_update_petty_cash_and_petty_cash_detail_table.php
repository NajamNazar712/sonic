<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePettyCashAndPettyCashDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash_statements', function ($table) {
            $table->integer('hub_id')->nullable()->change();
            $table->string('from')->nullable()->change();
            $table->string('to')->nullable()->change();
            $table->unsignedInteger('sdn_id')->index();
        });

        Schema::table('petty_cash_statement_details', function ($table) {
            $table->unsignedInteger('zone_id')->index();
            $table->unsignedInteger('city_id')->index();
            $table->unsignedInteger('employee_id')->index();
            $table->string('employee_name');
            $table->string('employee_designation');
            $table->string('reference_document_2')->nullable();
        });

        Schema::table('petty_cash_statement_drafts', function ($table) {
            $table->integer('hub_id')->nullable()->change();
            $table->string('from')->nullable()->change();
            $table->string('to')->nullable()->change();
            $table->unsignedInteger('sdn_id')->index();
        });

        Schema::table('petty_cash_statement_detail_drafts', function ($table) {
            $table->unsignedInteger('zone_id')->index();
            $table->unsignedInteger('city_id')->index();
            $table->unsignedInteger('employee_id')->index();
            $table->string('employee_name');
            $table->string('employee_designation');
            $table->string('reference_document_2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petty_cash_statements', function ($table) {
            $table->integer('hub_id')->change();
            $table->string('from')->change();
            $table->string('to')->change();
            if (Schema::hasColumn('petty_cash_statements', 'sdn_id')) {
                $table->dropColumn('sdn_id');
            }
        });

        Schema::table('petty_cash_statement_details', function ($table) {
            if (Schema::hasColumn('petty_cash_statement_details', 'zone_id')) {
                $table->dropColumn('zone_id');
            }
            if (Schema::hasColumn('petty_cash_statement_details', 'city_id')) {
                $table->dropColumn('city_id');
            }
            if (Schema::hasColumn('petty_cash_statement_details', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
            if (Schema::hasColumn('petty_cash_statement_details', 'employee_name')) {
                $table->dropColumn('employee_name');
            }
            if (Schema::hasColumn('petty_cash_statement_details', 'employee_designation')) {
                $table->dropColumn('employee_designation');
            }
        });

        Schema::table('petty_cash_statement_drafts', function ($table) {
            $table->integer('hub_id')->change();
            $table->string('from')->change();
            $table->string('to')->change();
            $table->dropColumn('sdn_id');
        });

        Schema::table('petty_cash_statement_detail_drafts', function ($table) {
            $table->dropColumn('zone_id');
            $table->dropColumn('city_id');
            $table->dropColumn('employee_id');
            $table->dropColumn('employee_name');
            $table->dropColumn('employee_designation');
        });
    }
}
