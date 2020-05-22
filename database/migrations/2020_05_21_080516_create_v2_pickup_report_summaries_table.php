<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2PickupReportSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_pickup_report_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date');
            $table->integer('pickup_request_id');
            $table->integer('total');
            $table->integer('pending_operations');
            $table->integer('pending_sales');
            $table->integer('before_cut_off_time');
            $table->integer('after_cut_off_time');
            $table->integer('attempted_and_picked');
            $table->integer('attempted_and_not_picked');
            $table->integer('attempted_failed');
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
        Schema::dropIfExists('v2_pickup_report_summaries');
    }
}
