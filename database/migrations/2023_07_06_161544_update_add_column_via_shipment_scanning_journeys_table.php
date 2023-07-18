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
        //     $table->index('shipment_id');
        //     $table->index('screen_location_id');
        //     $table->index('admin_id');
        //     $table->index('user_id');
        //     $table->index('substitute_user_id');
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
        //     $table->dropIndex('shipment_scanning_journeys_shipment_id_index');
        //     $table->dropIndex('shipment_scanning_journeys_screen_location_id_index');
        //     $table->dropIndex('shipment_scanning_journeys_admin_id_index');
        //     $table->dropIndex('shipment_scanning_journeys_user_id_index');
        //     $table->dropIndex('shipment_scanning_journeys_substitute_user_id_index');
        // });
    }
}
