<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeShiftsTableAddShiftTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            $table->integer('shift_type_id')->default(1)->index()->after('id');
        });     
    }

    /**
     * Reverse the migrations.  
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
