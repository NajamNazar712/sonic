<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRiderReturnDeliveryForRiderAudioRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rider_return_deliveries', function (Blueprint $table) {
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
        Schema::table('rider_return_deliveries', function (Blueprint $table) {
            $table->dropColumn('audio_path');
        });
    }
}
