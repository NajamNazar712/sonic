<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRvShipmentTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->boolean('is_bot')->default(0)->comment('0 stand for manual,1 stand for bot call')->after('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('rv_shipment_tickets', function (Blueprint $table) {
            $table->dropColumn('is_bot');
        });  
    }
}
