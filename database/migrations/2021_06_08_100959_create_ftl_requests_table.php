<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtlRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftl_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index()->nullable();
            $table->integer('shipper_id')->index()->nullable();
            $table->integer('salesperson_id')->index()->nullable();
            $table->string('shipper_name')->nullable();
            $table->integer('origin_id')->index();
            $table->integer('destination_id')->index();
            $table->float('weight');
            $table->integer('quantity');
            $table->integer('vendor_id')->index()->nullable();
            $table->integer('vehicle_id')->index();
            $table->integer('status_id')->index()->default(1);
            $table->float('freight_cost');
            $table->float('freight_charges');
            $table->float('gst');
            $table->float('total_charges');
            $table->timestamp('date');
            $table->integer('updated_by')->index()->nullable();
            $table->timestamp('updated_on');
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
        Schema::dropIfExists('ftl_requests');
    }
}
