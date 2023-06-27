<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRidersAndEmployeesTableForAreaId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->integer('area_id')->index()->nullable();
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('area_id')->index()->nullable();
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->integer('area_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('area_id');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('area_id');
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('area_id');
        });
    }
}
