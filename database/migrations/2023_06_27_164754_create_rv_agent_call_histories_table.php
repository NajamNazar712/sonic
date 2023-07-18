<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvAgentCallHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_agent_call_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rv_shipment_assign_agent_id')->index();
            $table->integer('call_finding_id')->index();
            $table->integer('call_to_id')->index();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('rv_agent_call_histories');
    }
}
