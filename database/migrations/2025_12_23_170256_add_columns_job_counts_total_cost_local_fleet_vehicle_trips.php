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
        Schema::table('local_fleet_vehicle_trips', function (Blueprint $table) {
           $table->date('trip_date')->nullable()->after('vehicle_id');
           $table->integer('total_dn_count')->default(0);
           $table->integer('total_rn_count')->default(0);
           $table->integer('total_pickup_count')->default(0);
           $table->decimal('total_trip_cost',12,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_fleet_vehicle_trips', function (Blueprint $table) {
            $table->dropColumn('trip_date');
            $table->dropColumn('total_dn_count');
            $table->dropColumn('total_rn_count');
            $table->dropColumn('total_pickup_count');
            $table->dropColumn('total_trip_cost');
        });
    }
};
