<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoManifestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_manifests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_hub_id')->index();
            $table->integer('destination_hub_id')->index();
            $table->integer('shipping_mode_id')->index();
            $table->integer('transport_mode_id')->index();
            $table->integer('bags');
            $table->integer('shipments');
            $table->integer('quantity')->nullable();
            $table->decimal('bags_weight', 16,2);
            $table->decimal('actual_weight', 16,2);
            $table->integer('status_id');
            $table->integer('vehicle_type');
            $table->integer('created_by')->index();
            $table->integer('received_by')->index()->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
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
        Schema::dropIfExists('cargo_manifests');
    }
}
