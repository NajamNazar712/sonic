<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRidersIncentivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders_incentives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->timestamp('date')->index();
            $table->integer('pickup_shipments');
            $table->decimal('pickup_incentive');
            $table->integer('delivery_shipments');
            $table->decimal('delivery_incentive');
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
        Schema::dropIfExists('riders_incentives');
    }
}
