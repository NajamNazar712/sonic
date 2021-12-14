<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeBankForFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_bank_informations', function (Blueprint $table) {
            $table->string('branch_code')->nullable()->change();
            $table->string('account_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_bank_informations', function (Blueprint $table) {
            $table->string('branch_code')->nullable(false)->change();
            $table->string('account_no')->nullable(false)->change();
        });
    }
}
