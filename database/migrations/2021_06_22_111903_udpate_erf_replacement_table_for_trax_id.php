<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UdpateErfReplacementTableForTraxId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_requisition_replacements', function (Blueprint $table) {
            $table->string('trax_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_requisition_replacements', function (Blueprint $table) {
            $table->string('trax_id')->change();
        });
    }
}
