<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvAssignAgentSubStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_assign_agent_sub_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('rv_assign_agent_status_id')->index();
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('rv_assign_agent_sub_statuses');
    }
}
