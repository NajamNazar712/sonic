<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MarchBranchesForSunday extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->integer('piece_id')->nullable()->index();
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->Decimal('fintech_charges')->after('esc_charges')->nullable();
        });
        Schema::table('v2_pickup_notes', function (Blueprint $table) {
            $table->integer('shipments')->default(0);
            $table->integer('arrived_shipments')->default(0);
            $table->integer('shipments_scanned_by_rider')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->dropColumn('piece_id');
        });
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('fintech_charges');
        });
        Schema::table('v2_pickup_notes', function (Blueprint $table) {
            $table->dropColumn('shipments');
            $table->dropColumn('arrived_shipments');
            $table->dropColumn('shipments_scanned_by_rider');
        });
    }
}
