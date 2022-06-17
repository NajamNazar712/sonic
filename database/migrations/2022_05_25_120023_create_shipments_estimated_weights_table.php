<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsEstimatedWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments_estimated_weights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->decimal('estimated_weight', 8, 2);
            $table->decimal('actual_weight', 8, 2);
            $table->decimal('length', 8, 2)->nullable()->default(NULL);
            $table->decimal('breadth', 8, 2)->nullable()->default(NULL);
            $table->decimal('height', 8, 2)->nullable()->default(NULL);
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
        Schema::dropIfExists('shipments_estimated_weights');
    }
}
