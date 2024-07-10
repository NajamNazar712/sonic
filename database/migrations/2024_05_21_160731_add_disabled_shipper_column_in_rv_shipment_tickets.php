<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisabledShipperColumnInRvShipmentTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->boolean('disabled_shipper')->nullable()->default(false)->after('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->dropColumn('disabled_shipper');
        });
    }
}
