<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateV2PickupJourneyForReferenceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_v2_pickup_journeys', function (Blueprint $table) {
            $table->integer('reference_1_id')->nullable();
            $table->integer('reference_2_id')->nullable();
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
            $table->dropColumn('reference_1_id');
            $table->dropColumn('reference_2_id');
        });
    }
}
