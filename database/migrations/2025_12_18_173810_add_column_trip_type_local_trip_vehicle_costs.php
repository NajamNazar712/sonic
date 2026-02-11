<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_trip_vehicle_costs', function (Blueprint $table) {
            $table->tinyInteger('trip_type')->comment('0-out,1-in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_trip_vehicle_costs', function (Blueprint $table) {
           $table->dropColumn('trip_type');
        });
    }
};
