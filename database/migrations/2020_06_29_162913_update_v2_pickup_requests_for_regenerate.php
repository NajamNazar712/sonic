<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateV2PickupRequestsForRegenerate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_pickup_requests', function (Blueprint $table) {
            $table->tinyInteger('regenerate')->default(0);
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
            $table->dropColumn('regenerate');
        });
    }
}
