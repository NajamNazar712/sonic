<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShipmentStatusReasonIdColumnRvAssignAgentSubStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_assign_agent_sub_statuses', function (Blueprint $table) {
            $table->integer('shipment_status_reason_id')->index()->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_assign_agent_sub_statuses', function (Blueprint $table) {
            $table->dropColumn('shipment_status_reason_id');
        });
    }
}
