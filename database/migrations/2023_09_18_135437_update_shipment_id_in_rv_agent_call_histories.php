<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentIdInRvAgentCallHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_agent_call_histories', function (Blueprint $table) {
            $table->integer('shipment_id')->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_agent_call_histories', function (Blueprint $table) {
            $table->dropColumn('shipment_id');
        });
    }
}
