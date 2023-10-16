<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnresponsiveEmailTimeRvShipmentAssignAgent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_assign_agents', function (Blueprint $table) {
            $table->dateTime('unresponsive_email_time')->nullable()->after('unresponsive_email_count');
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
            $table->dropColumn('unresponsive_email_time');
        });
    }
}
