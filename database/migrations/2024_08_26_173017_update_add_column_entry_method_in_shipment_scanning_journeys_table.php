<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnEntryMethodInShipmentScanningJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->string('entry_method')->nullable()->after('updated_via');
        });

        Schema::table('bag_scanning_journeys', function (Blueprint $table) {
            $table->string('entry_method')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
            $table->dropColumn('entry_method');
        });

        Schema::table('bag_scanning_journeys', function (Blueprint $table) {
            $table->dropColumn('entry_method');
        });
    }
}
