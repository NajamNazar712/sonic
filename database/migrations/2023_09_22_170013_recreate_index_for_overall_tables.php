<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateIndexForOverallTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['booking_type_id']);
            $table->dropIndex(['consignee_city_id']);
            $table->dropIndex(['shipper_status_id']);
            $table->dropIndex(['business_category_id']);
            $table->dropIndex(['shipping_mode_id']);
            $table->dropIndex(['pickup_address_id']);
            $table->dropIndex(['user_id']);
            $table->index('booking_type_id');
            $table->index('consignee_city_id');
            $table->index('shipper_status_id');
            $table->index('business_category_id');
            $table->index('shipping_mode_id');
            $table->index('pickup_address_id');
            $table->index('user_id');
        });

        Schema::table('shipments_journey', function (Blueprint $table) {
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['shipper_status_id']);
            $table->dropIndex(['status_reason_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['reference_1_id']);
            $table->index('shipment_id');
            $table->index('shipper_status_id');
            $table->index('status_reason_id');
            $table->index('city_id');
            $table->index('reference_1_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
