<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvShipmentAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_shipment_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id')->index();
            $table->integer('total_shipments')->default(0);
            $table->integer('actual_productivity')->default(0);
            $table->integer('already_updated')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('rv_shipment_agents');
    }
}
