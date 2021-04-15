<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmployeeDesignationForCodeAndDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_designations', function (Blueprint $table) {
            $table->string('code')->index()->nullable();
            $table->string('description')->nullable();
            $table->integer('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_designations', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('description');
            $table->dropColumn('status');
        });
    }
}
