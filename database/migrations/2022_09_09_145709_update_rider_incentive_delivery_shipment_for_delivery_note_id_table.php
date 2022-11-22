<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderIncentiveDeliveryShipmentForDeliveryNoteIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_incentive_delivery_shipments', function (Blueprint $table) {
            $table->integer('delivery_note_id')->after('shipment_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rider_incentive_delivery_shipments', function (Blueprint $table) {
            $table->dropColumn('delivery_note_id');
        });
    }
}
