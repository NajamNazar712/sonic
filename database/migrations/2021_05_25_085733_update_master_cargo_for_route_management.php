<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMasterCargoForRouteManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->dropColumn('junction_hub_1_id');
            $table->dropColumn('junction_hub_2_id');
            $table->dropColumn('vehicle');
            $table->integer('route_management_id')->index();
            $table->integer('fleet_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_cargoes', function (Blueprint $table) {
            $table->integer('junction_hub_1_id');
            $table->integer('junction_hub_2_id');
            $table->string('vehicle');
            $table->dropColumn('fleet_id');
            $table->dropColumn('route_management_id');

        });
    }
}
