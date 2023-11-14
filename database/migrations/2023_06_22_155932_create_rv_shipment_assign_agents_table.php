<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvShipmentAssignAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_shipment_assign_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('shipments_journey_id')->index();
            $table->integer('last_shipments_journey_id')->index();
            $table->integer('rv_assign_agent_status_id')->index()->nullable();
            $table->integer('rv_assign_agent_sub_status_id')->index()->nullable();
            $table->integer('rv_state_id')->index();
            $table->boolean('is_fake_status')->default(0);
            $table->integer('rv_fake_status_id')->index()->nullable();
            $table->integer('rv_shipment_agent_id')->index();
            $table->integer('updated_type_id')->index();
            $table->integer('updated_by_id')->index();
            $table->string('remarks');
            $table->integer('call_to_id')->default(0);
            $table->date('state_date');
            $table->integer('unresponsive_count')->default(0);
            $table->integer('unresponsive_email_count')->default(0);
            $table->dateTime('unresponsive_attempt_time')->nullable();
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
        Schema::dropIfExists('rv_shipment_assign_agents');
    }
}