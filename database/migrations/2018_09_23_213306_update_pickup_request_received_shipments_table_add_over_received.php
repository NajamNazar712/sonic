<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePickupRequestReceivedShipmentsTableAddOverReceived extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pickup_request_received_shipments', function (Blueprint $table) {
            $table->tinyinteger('over_received')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_request_received_shipments', function (Blueprint $table) {
            $table->dropColumn('over_received');
        });
    }
}
