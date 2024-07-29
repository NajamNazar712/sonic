<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstCallTimeMinutesInRvShipmentAssignAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_assign_agents', function (Blueprint $table) {
            $table->unsignedTinyInteger('first_call_time_mins')->default(0)->nullable()->after('call_count');
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
            $table->dropColumn('first_call_time_mins');
        });
    }
}
