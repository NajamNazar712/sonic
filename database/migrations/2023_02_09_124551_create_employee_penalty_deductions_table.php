<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePenaltyDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_penalty_deductions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->index();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->integer('leave_without_pay');
            $table->integer('deduction_from_leave_quota');
            $table->integer('line_manager_id')->index();
            $table->tinyInteger('status')->index();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('employee_penalty_deductions');
    }
}
