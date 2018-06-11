<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentJourneyTableShipmentStatusReasonId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_journey', function (Blueprint $table) {
            $table->integer('shipment_status_reason_id')->nullable()->default(NULL)->after('consignee_status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_journey', function (Blueprint $table) {
            $table->dropColumn('shipment_status_reason_id');
        });
    }
}
