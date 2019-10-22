<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderPickupsTableAddShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_pickups', function (Blueprint $table) {
            $table->integer('shipments')->nullable()->default(NULL)->after('distance_from_current_to_actual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rider_pickups', function (Blueprint $table) {
            $table->dropColumn('shipments');
        });
    }
}
