<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLengthOfEmployeeAttachmentsVarcharColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_attachments', function ($table) {
            $table->string('cv', 350)->change();
            $table->string('cnic', 350)->change();
            $table->string('photo', 350)->change();
            $table->string('academic', 350)->change();
            $table->string('experience', 350)->change();
            $table->string('last_pay_slip', 350)->change();
            $table->string('nikkah_nama', 350)->change();
            $table->string('cnic_spouse', 350)->change();
            $table->string('child_b_form', 350)->change();
            $table->string('cnic_nominee', 350)->change();
            $table->string('utility_bill', 350)->change();
            $table->string('affidavit', 350)->change();
            $table->string('cheque', 350)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_attachments', function ($table) {
            $table->string('cv', 191)->change();
            $table->string('cnic', 191)->change();
            $table->string('photo', 191)->change();
            $table->string('academic', 191)->change();
            $table->string('experience', 191)->change();
            $table->string('last_pay_slip', 191)->change();
            $table->string('nikkah_nama', 191)->change();
            $table->string('cnic_spouse', 191)->change();
            $table->string('child_b_form', 191)->change();
            $table->string('cnic_nominee', 191)->change();
            $table->string('utility_bill', 191)->change();
            $table->string('affidavit', 191)->change();
            $table->string('cheque', 191)->change();
        });
    }
}
