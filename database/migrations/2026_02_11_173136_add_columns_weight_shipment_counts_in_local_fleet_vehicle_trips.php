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
            $table->integer('total_dn_shipment_count')->after('total_dn_count')->default(0);
            $table->decimal('total_dn_shipment_weight',8,2)->after('total_dn_shipment_count')->default(0);

            $table->integer('total_rn_shipment_count')->after('total_rn_count')->default(0);
            $table->decimal('total_rn_shipment_weight',8,2)->after('total_rn_shipment_count')->default(0);

            $table->integer('total_pickup_shipment_count')->after('total_pickup_count')->default(0);
            $table->decimal('total_pickup_shipment_weight',8,2)->after('total_pickup_shipment_count')->default(0);
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
            //
        });
    }
};
