<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStationRecoveryReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_recovery_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date');
            $table->integer('city_id');
            $table->integer('zone_id');
            $table->integer('delivered_shipments');
            $table->decimal('last_day_balance');
            $table->decimal('amount');
            $table->decimal('total_amount');
            $table->decimal('deposit_amount');
            $table->decimal('adjustment_amount');
            $table->decimal('difference_amount');
            $table->decimal('percentage');
            $table->string('reason');
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
        Schema::dropIfExists('station_recovery_reports');
    }
}
