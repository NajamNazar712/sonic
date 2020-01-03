<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyFakeStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_fake_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('zone_id');
            $table->integer('delivery_note_id');
            $table->integer('hub_id');
            $table->integer('rider_id');
            $table->integer('total_delivery_notes');
            $table->integer('total_shipments');
            $table->integer('total_undelivered_shipments');
            $table->integer('total_fake_status_shipments');
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
        Schema::dropIfExists('daily_fake_statuses');
    }
}
