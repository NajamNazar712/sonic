<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMigrationForV2PickupRequestForReversePickup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->integer('reverse_pickup')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->dropColumn('reverse_pickup');
        });
    }
}
