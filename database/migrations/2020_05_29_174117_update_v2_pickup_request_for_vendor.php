<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateV2PickupRequestForVendor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->boolean('vendor')->nullable();
            $table->boolean('try_and_buy')->nullable();
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
            $table->dropColumn('vendor');
            $table->dropColumn('try_and_buy');
        });
    }
}
