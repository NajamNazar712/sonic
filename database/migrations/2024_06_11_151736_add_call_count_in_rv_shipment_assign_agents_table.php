<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallCountInRvShipmentAssignAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //for adding call_count column in rv_shipment_assign_agents table
        Schema::table('rv_shipment_assign_agents', function (Blueprint $table) {
            $table->unsignedTinyInteger('call_count')->default(0)->nullable()->after('agent_id');
        });

        //for adding call_count column in rv_shipment_assign_agent_details table
        Schema::table('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('call_count')->nullable()->after('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_shipment_assign_agents', function (Blueprint $table) {
            $table->dropColumn('call_count');
        });

        Schema::table('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->dropColumn('call_count');
        });
    }
}
