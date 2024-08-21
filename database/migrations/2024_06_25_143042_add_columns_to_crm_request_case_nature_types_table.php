<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCrmRequestCaseNatureTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crm_request_case_nature_types', function (Blueprint $table) {
            $table->boolean('remarks_visibility')->default(0)->after('updated_at');
            $table->boolean('shipper_visibility')->default(0)->after('remarks_visibility');
            $table->string('shipment_status')->nullable()->after('shipper_visibility');
            $table->string('admin_departments')->nullable()->after('shipment_status');
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
            $table->dropColumn('remarks_visibility');
            $table->dropColumn('shipper_visibility');
            $table->dropColumn('shipment_status');
            $table->dropColumn('admin_departments');
        });
    }
}
