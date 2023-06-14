<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmAgentAutoAssignShippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_agent_auto_assign_shippers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id')->index();
            $table->integer('shipper_key_id')->index();
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
        Schema::dropIfExists('crm_agent_auto_assign_shippers');
    }
}
