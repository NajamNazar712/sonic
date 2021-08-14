<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentsPickupJourney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_v2_pickup_journeys', function (Blueprint $table) {
            $table->integer('reason_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments_v2_pickup_journeys', function (Blueprint $table) {
            $table->dropColumn('reason_id');
        });
    }
}
