<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeliveryLocationMappingTableAddCityArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_location_mappings', function (Blueprint $table) {
            $table->Integer('city_area_id')->after('area_name')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_location_mappings', function (Blueprint $table) {
            $table->dropColumn('city_area_id');
        });
    }
}
