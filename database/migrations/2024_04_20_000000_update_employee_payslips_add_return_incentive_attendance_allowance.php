<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeePayslipsAddReturnIncentiveAttendanceAllowance extends Migration
{
    public function up()
    {
        Schema::table('employee_payslips', function (Blueprint $table) {
            $table->integer('return_incentive')->nullable()->after('sales_incentive');
            $table->integer('attendance_allowance')->nullable()->after('return_incentive');
        });
    }

    public function down()
    {
        Schema::table('employee_payslips', function (Blueprint $table) {
            $table->dropColumn('return_incentive');
            $table->dropColumn('attendance_allowance');
        });
    }
}
