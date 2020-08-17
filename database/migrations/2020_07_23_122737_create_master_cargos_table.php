<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_cargoes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_hub_id');
            $table->integer('destination_hub_id');
            $table->integer('junction_hub_1_id');
            $table->integer('junction_hub_2_id')->nullable();
            $table->integer('shipping_mode_id');
            $table->integer('transport_mode_id');
            $table->integer('transport_mode_vendor_id');
            $table->string('builty_number')->nullable();
            $table->integer('bags');
            $table->integer('shipments');
            $table->decimal('bags_weight', 16,2);
            $table->decimal('actual_weight', 16,2);
            $table->integer('type');
            $table->integer('status_id');
            $table->string('driver_name');
            $table->string('vehicle');
            $table->string('phone_number');
            $table->integer('created_by');
            $table->integer('received_bags')->nullable();
            $table->integer('short_received_bags')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->integer('received_by')->nullable();
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
        Schema::dropIfExists('master_cargoes');
    }
}
