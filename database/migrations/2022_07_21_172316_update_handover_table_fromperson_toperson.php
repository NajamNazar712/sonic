<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHandoverTableFrompersonToperson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('handovers', function (Blueprint $table) {
            $table->string('from_dept_area_desg')->after("from")->nullable();
            $table->string('to_dept_area_desg')->after("to")->nullable();
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
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropColumn('from_person');
            $table->dropColumn('to_person');
        });
    }
}
