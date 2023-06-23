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
            $table->integer('petty_cash_id')->index();
            $table->integer('status')->index()->default(0);
            $table->integer('account_head_id')->index();
            $table->integer('updated_by')->index();
            $table->integer('account_title_id')->index();
            $table->integer('city_id')->index();
            $table->integer('employee_id')->index();
            $table->string('employee_name')->nullable();
            $table->string('employee_designation')->nullable();
            $table->integer('dncc_id')->index()->nullable();
            $table->integer('delivered_shipments')->nullable();
            $table->string('expense_details');
            $table->double('amount');
            $table->integer('reference_no')->nullable();
            $table->string('remarks')->nullable();
            $table->string('reference_document')->nullable();
            $table->string('reference_document_2')->nullable();
            $table->integer('edit_by')->index()->nullable();
            $table->timestamp('edit_at')->index()->nullable();
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
