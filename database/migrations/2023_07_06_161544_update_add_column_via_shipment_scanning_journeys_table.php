<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColumnViaShipmentScanningJourneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
        //     $table->smallInteger('updated_via')->nullable()->after('substitute_user_id')->index();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('shipment_scanning_journeys', function (Blueprint $table) {
        //     $table->dropColumn('updated_via');
        // });
    }
}
