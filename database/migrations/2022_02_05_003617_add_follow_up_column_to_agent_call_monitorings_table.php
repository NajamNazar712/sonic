<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFollowUpColumnToAgentCallMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_call_monitorings', function (Blueprint $table) {
            $table->dateTime('follow_up')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_call_monitorings', function (Blueprint $table) {
            $table->dropColumn('follow_up');
        });
    }
}
