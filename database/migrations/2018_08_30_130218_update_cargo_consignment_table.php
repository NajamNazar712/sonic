<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCargoConsignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->renameColumn('origin_city_id', 'origin_hub_id');
            $table->renameColumn('destination_city_id', 'destination_hub_id');
            $table->renameColumn('junction_city_1_id', 'junction_hub_1_id');
            $table->renameColumn('junction_city_2_id', 'junction_hub_2_id');
            $table->dropColumn('hub_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cargo_consignments', function (Blueprint $table) {
            $table->renameColumn('origin_hub_id', 'origin_city_id');
            $table->renameColumn('destination_hub_id', 'destination_city_id');
            $table->renameColumn('junction_hub_1_id', 'junction_city_1_id');
            $table->renameColumn('junction_hub_2_id', 'junction_city_2_id');
            $table->integer('hub_id')->after('junction_city_2_id');
        });
    }
}
