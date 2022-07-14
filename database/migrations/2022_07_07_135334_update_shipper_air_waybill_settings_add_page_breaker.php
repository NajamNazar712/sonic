<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipperAirWaybillSettingsAddPageBreaker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipper_air_waybill_settings', function (Blueprint $table) {
            $table->tinyInteger('page_breaker')->default(0)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipper_air_waybill_settings', function (Blueprint $table) {
            $table->dropColumn('page_breaker');
        });
    }
}
