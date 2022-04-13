<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeesForNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->Integer('employee_nature_id')->nullable()->index();
            $table->Integer('replacement_employee_id')->nullable()->index();
            $table->Integer('replacement_last_working_day')->nullable();
            $table->Integer('fuel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('employee_nature_id');
            $table->dropColumn('replacement_employee_id');
            $table->dropColumn('replacement_last_working_day');
            $table->dropColumn('fuel');
        });
    }
}
