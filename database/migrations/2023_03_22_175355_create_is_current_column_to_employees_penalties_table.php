<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIsCurrentColumnToEmployeesPenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_penalties', function (Blueprint $table) {
            $table->boolean('is_current_record')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_penalties', function (Blueprint $table) {
            $table->dropColumn('is_current_record');
        });
    }
}
