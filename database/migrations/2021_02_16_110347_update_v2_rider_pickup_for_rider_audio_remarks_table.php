<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateV2RiderPickupForRiderAudioRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v2_rider_pickups', function (Blueprint $table) {
            $table->string('audio_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v2_rider_pickups', function (Blueprint $table) {
            $table->dropColumn('audio_path');
        });
    }
}
