<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsFromemployeeBankInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_bank_informations', function (Blueprint $table) {
            $table->dropColumn('branch_code');
            $table->dropColumn('account_no');
            $table->dropColumn('branch_name');
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
            $table->string('branch_code')->nullable();
            $table->string('account_no')->nullable();
            $table->string('branch_name')->nullable();
        });
    }
}
