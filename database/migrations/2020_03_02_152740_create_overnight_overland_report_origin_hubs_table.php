<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOvernightOverlandReportOriginHubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overnight_overland_report_origin_hubs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_id');
            $table->integer('hub_id');
            $table->integer('shipping_mode_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overnight_overland_report_origin_hubs');
    }
}
