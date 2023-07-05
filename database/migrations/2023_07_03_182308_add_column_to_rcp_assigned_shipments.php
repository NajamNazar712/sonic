<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToRcpAssignedShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rcp_assigned_shipments', function (Blueprint $table) {
            $table->integer('substitute_user_id')->index()->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rcp_assigned_shipments', function (Blueprint $table) {
            $table->dropColumn('substitute_user_id');
        });
    }
}
