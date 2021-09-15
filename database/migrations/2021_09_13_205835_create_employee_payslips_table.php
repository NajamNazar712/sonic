<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_payslips', function (Blueprint $table) {
            $table->increments('id');
            $table->date('payroll_month')->index();
            $table->date('payroll_cut_off_date');
            $table->string('trax_id')->index();
            $table->string('name');
            $table->string('designation');
            $table->string('department');
            $table->string('hub');
            $table->string('zone')->nullable();
            $table->date('joining_date');
            $table->date('confirmation_date')->nullable();
            $table->string('cnic');
            $table->string('employee_status')->nullable();
            $table->integer('payroll_days')->nullable();
            $table->integer('present_days')->nullable();
            $table->integer('pay_cut_days')->nullable();
            $table->integer('absent_days')->nullable();
            $table->integer('extra_paid_days')->nullable();
            $table->integer('fuel_days')->nullable();
            $table->integer('basic_salary');
            $table->integer('house_rent')->nullable();
            $table->integer('medical')->nullable();
            $table->integer('gross_salary')->nullable();
            $table->integer('mobile_allowance')->nullable();
            $table->integer('vehicle_allowance')->nullable();
            $table->integer('fuel_allowance')->nullable();
            $table->integer('conveyance_allowance')->nullable();
            $table->integer('vehicle_maintenance')->nullable();
            $table->integer('fixed_incentive')->nullable();
            $table->integer('holiday_allowance')->nullable();
            $table->integer('overtime')->nullable();
            $table->integer('bonus')->nullable();
            $table->integer('arrears')->nullable();
            $table->integer('pickup_incentive')->nullable();
            $table->integer('delivery_incentive')->nullable();
            $table->integer('operation_incentive')->nullable();
            $table->integer('extra_duty_allowance')->nullable();
            $table->integer('others_addition')->nullable();
            $table->integer('total_salary');
            $table->integer('paycut')->nullable();
            $table->integer('absent')->nullable();
            $table->integer('late_deduction')->nullable();
            $table->integer('income_tax')->nullable();
            $table->integer('eobi')->nullable();
            $table->integer('advance_salary')->nullable();
            $table->integer('month_closing')->nullable();
            $table->integer('loan')->nullable();
            $table->integer('fuel_card')->nullable();
            $table->integer('open_parcel')->nullable();
            $table->integer('phone_call')->nullable();
            $table->integer('recovery')->nullable();
            $table->integer('auction_sale')->nullable();
            $table->integer('penalty')->nullable();
            $table->integer('other_deductions')->nullable();
            $table->integer('van_deduction')->nullable();
            $table->integer('medical_insurance')->nullable();
            $table->integer('total_deduction')->nullable();
            $table->integer('net_salary')->nullable();
            $table->integer('iban')->nullable();
            $table->integer('added_by')->nullable()->index();
            $table->string('employee_type')->nullable()->index();
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
        Schema::dropIfExists('employee_payslips');
    }
}
