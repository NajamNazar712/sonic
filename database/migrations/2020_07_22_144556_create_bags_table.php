<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seal_number');
            $table->integer('origin_hub_id');
            $table->integer('destination_hub_id');
            $table->integer('junction_hub_1_id');
            $table->integer('junction_hub_2_id')->nullable();
            $table->integer('shipping_mode_id');
            $table->integer('transport_mode_id');
            $table->integer('transport_mode_vendor_id');
            $table->string('builty_number')->nullable();
            $table->timestamp('expected_arrival_date')->nullable();
            $table->integer('shipments');
            $table->decimal('shipments_weight', 16, 2);
            $table->decimal('actual_weight', 16, 2);
            $table->integer('type');
            $table->integer('status_id');
            $table->integer('created_by');
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
        Schema::dropIfExists('bags');
    }
}
