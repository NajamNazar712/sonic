<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShipmentsPickupJourneyTableIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_pickup_journey', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('shipment_id');
            $table->index('status_id');
            $table->index('admin_id');
            $table->index('reference_1_id');
            $table->index('reference_2_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_pickup_journey', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['shipment_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['admin_id']);
            $table->dropIndex(['reference_1_id']);
            $table->dropIndex(['reference_2_id']);
        });
    }
}
