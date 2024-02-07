<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAssignedByInRvShipmentAssignAgentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->integer('assigned_by')->index()->nullable()->after('call_to_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->dropColumn('assigned_by');
        });
    }
}
