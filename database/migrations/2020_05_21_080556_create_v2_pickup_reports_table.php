<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateV2PickupReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_pickup_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date');
            $table->integer('pickup_request_id');
            $table->integer('status_id');
            $table->integer('sale_person_id');
            $table->integer('expected_shipments');
            $table->integer('received_shipments');
            $table->integer('difference_shipments');
            $table->integer('department_id');
            $table->integer('legend_id');
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
        Schema::dropIfExists('v2_pickup_reports');
    }
}
