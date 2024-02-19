<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssignedToTypeIdInRvShipmentAssignAgentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->integer('assigned_to_type_id')->index()->after('call_to_id')->nullable();
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
            $table->dropColumn('assigned_to_type_id');
        });
    }
}
