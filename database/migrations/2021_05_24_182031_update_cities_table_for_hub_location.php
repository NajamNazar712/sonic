<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCitiesTableForHubLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('hub_location_latitude', 10, 6)->nullable()->default(NULL);
            $table->decimal('hub_location_longitude', 10, 6)->nullable()->default(NULL);
        });
        Schema::table('city_histories', function (Blueprint $table) {
            $table->decimal('hub_location_latitude', 10, 6)->nullable()->default(NULL);
            $table->decimal('hub_location_longitude', 10, 6)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('hub_location_latitude');
            $table->dropColumn('hub_location_longitude');
        });
        Schema::table('city_histories', function (Blueprint $table) {
            $table->dropColumn('hub_location_latitude');
            $table->dropColumn('hub_location_longitude');
        });
    }
}
