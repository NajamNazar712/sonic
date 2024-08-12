<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsInCrmRequestCaseNatureTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->longText('shipment_status')->nullable()->change();
            $table->longText('admin_departments')->nullable()->change();
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
            $table->string('shipment_status')->nullable()->change();
            $table->string('admin_departments')->nullable()->change();
        });
    }
}
