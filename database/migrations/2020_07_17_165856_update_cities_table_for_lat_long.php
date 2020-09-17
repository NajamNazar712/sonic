<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCitiesTableForLatLong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city_histories', function (Blueprint $table) {
            $table->decimal('location_latitude', 10, 6)->nullable();
            $table->decimal('location_longitude', 10, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_histories', function (Blueprint $table) {
            $table->dropColumn('location_latitude');
            $table->dropColumn('location_longitude');
        });
    }
}
