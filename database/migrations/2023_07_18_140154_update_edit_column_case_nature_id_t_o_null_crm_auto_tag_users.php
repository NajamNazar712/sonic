<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEditColumnCaseNatureIdTONullCrmAutoTagUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_auto_tag_users', function (Blueprint $table) {
            $table->integer('crm_case_nature_id')->nullable()->change();
            $table->integer('crm_case_nature_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crm_auto_tag_users', function (Blueprint $table) {
            $table->integer('crm_case_nature_id')->nullable(false)->change();
            $table->integer('crm_case_nature_type_id')->nullable(false)->change();
        });
    }
}
