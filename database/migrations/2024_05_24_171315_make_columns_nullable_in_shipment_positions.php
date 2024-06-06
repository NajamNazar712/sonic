<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeColumnsNullableInShipmentPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_positions', function (Blueprint $table) {
            $table->integer('scanned_by_id')->nullable()->change();
            $table->integer('screen_location_id')->nullable()->change();

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
            $table->integer('scanned_by_id')->nullable(false)->change();
            $table->integer('screen_location_id')->nullable(false)->change();

        });
    }
}
