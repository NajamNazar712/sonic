<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddZoneIdColumnInRvAgent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_agent_assign_hubs', function (Blueprint $table) {
            $table->integer('zone_id')->after('city_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_agent_assign_hubs', function (Blueprint $table) {
            $table->dropColumn('zone_id');
        });
    }
}
