<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePayslipTableForNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_payslips', function (Blueprint $table) {
            $table->integer('total_salary')->nullable()->change();
            $table->integer('basic_salary')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_payslips', function (Blueprint $table) {
            $table->integer('total_salary')->change();
            $table->integer('basic_salary')->change();
        });
    }
}
