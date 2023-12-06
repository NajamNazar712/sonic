<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLongtextColumnsToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
//    public function up()
//    {
//        Schema::table('jobs', function (Blueprint $table) {
//            $table->text('payload')->comment(' ')->change();
//        });
//        Schema::table('failed_jobs', function (Blueprint $table) {
//            $table->text('exception')->comment(' ')->change();
//            $table->text('payload')->comment(' ')->change();
//        });
//        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
//            $table->text('remarks')->comment(' ')->change();
//        });
//        Schema::table('petty_cash_statement_detail_drafts', function (Blueprint $table) {
//            $table->text('remarks')->comment(' ')->change();
//        });
//        Schema::table('employee_requisition_status_logs', function (Blueprint $table) {
//            $table->text('reject_reason')->comment(' ')->change();
//        });
//        Schema::table('delivery_note_error_logs', function (Blueprint $table) {
//            $table->text('message')->comment(' ')->change();
//        });
//    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
//    public function down()
//    {
//        Schema::table('jobs', function (Blueprint $table) {
//            $table->longText('payload')->comment('')->change();
//        });
//        Schema::table('failed_jobs', function (Blueprint $table) {
//            $table->longText('exception')->comment('')->change();
//            $table->longText('payload')->comment('')->change();
//        });
//        Schema::table('petty_cash_statement_details', function (Blueprint $table) {
//            $table->longText('remarks')->comment('')->change();
//        });
//        Schema::table('petty_cash_statement_detail_drafts', function (Blueprint $table) {
//            $table->longText('remarks')->comment('')->change();
//        });
//        Schema::table('employee_requisition_status_logs', function (Blueprint $table) {
//            $table->longText('reject_reason')->comment('')->change();
//        });
//        Schema::table('delivery_note_error_logs', function (Blueprint $table) {
//            $table->longText('message')->comment('')->change();
//        });
//    }
}
