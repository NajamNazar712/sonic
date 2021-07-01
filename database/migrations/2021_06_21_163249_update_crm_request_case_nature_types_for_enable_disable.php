<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrmRequestCaseNatureTypesForEnableDisable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->smallInteger('status_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
}
