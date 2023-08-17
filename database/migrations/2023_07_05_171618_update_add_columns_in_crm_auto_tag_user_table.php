<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnsInCrmAutoTagUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_auto_tag_users', function (Blueprint $table) {
            $table->integer('city_area_id')->nullable()->index('city_area_id')->after('city_id');
            $table->integer('crm_case_nature_id')->index('crm_case_nature_id')->after('city_area_id');
            $table->integer('crm_case_nature_type_id')->index('crm_case_nature_type_id')->after('crm_case_nature_id');
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
            $table->dropColumn('city_area_id');
            $table->dropColumn('crm_case_nature_id');
            $table->dropColumn('crm_case_nature_type_id');
        });
    }
}
