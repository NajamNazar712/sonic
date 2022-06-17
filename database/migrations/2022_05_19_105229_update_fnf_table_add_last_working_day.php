<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFnfTableAddLastWorkingDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fnf_section_employees',function ($table){
            $table->date('last_working_date')->nullable();
            $table->boolean('employee_inactive')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fnf_section_employees', function (Blueprint $table) {
            $table->dropColumn('last_working_date');
            $table->dropColumn('employee_inactive');
        });
    }
}
