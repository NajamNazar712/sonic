<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRvAgentCallHistoryIdColumnInRvShipmentAssignAgentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->integer('rv_agent_call_history_id')->nullable()->index();
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
            $table->dropColumn('rv_agent_call_history_id');
        });
    }
}
