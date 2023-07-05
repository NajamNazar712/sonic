<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvShipmentAssignAgentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_shipment_assign_agent_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rv_shipment_assign_agent_id')->index('assign_agent_id_index');
            $table->integer('agent_id')->index();
            $table->integer('shipment_id')->index();
            $table->integer('rv_assign_agent_status_id')->index()->nullable();
            $table->integer('rv_assign_agent_sub_status_id')->index(null, 'sub_status_index')->nullable();
            $table->integer('rv_state_id')->index()->nullable();
            $table->integer('updated_type_id')->index()->nullable();
            $table->integer('updated_by_id')->index()->nullable();
            $table->boolean('is_fake_status')->default(0);
            $table->integer('rv_fake_status_id')->index()->nullable();
            $table->string('remarks');
            $table->integer('call_to_id')->index();
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
        Schema::dropIfExists('rv_shipment_assign_agent_details');
    }
}
