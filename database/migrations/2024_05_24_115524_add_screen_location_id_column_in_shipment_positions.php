<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScreenLocationIdColumnInShipmentPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_positions', function (Blueprint $table) {
            $table->unsignedBigInteger('screen_location_id')->after('screen_location');
            $table->unsignedBigInteger('scanned_by_id')->after('scanned_by');
            $table->unsignedBigInteger('scanned_by_user_type')->after('scanned_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_positions', function (Blueprint $table) {
            $table->dropColumn('screen_location_id');
            $table->dropColumn('scanned_by_id');
            $table->dropColumn('scanned_by_user_type');

        });
    }
}
