<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoManifestBagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo_manifest_bags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seal_number');
            $table->integer('origin_hub_id')->index();
            $table->integer('destination_hub_id')->index();
            $table->integer('shipments');
            $table->decimal('shipments_weight', 16, 2);
            $table->decimal('actual_weight', 16, 2);
            $table->integer('type');
            $table->integer('status_id')->index();
            $table->integer('quantity');
            $table->integer('transport_mode_id')->index();
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
        Schema::dropIfExists('cargo_manifest_bags');
    }
}
