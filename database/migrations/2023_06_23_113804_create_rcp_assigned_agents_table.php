<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRcpAssignedAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcp_assigned_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->index();
            $table->integer('status')->default('0');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('total_shipments')->default('0');
            $table->integer('assigned_shipments')->default('0');
            $table->integer('actual_productivity')->default('0');
            $table->integer('reattempt')->default('0');
            $table->integer('return')->default('0');
            $table->integer('intercept')->default('0');
            $table->integer('on_hold_for_self_collection')->default('0');
            $table->integer('unresponsive_return')->default('0');
            $table->integer('pending_shipments')->default('0');
            $table->integer('already_updated')->default('0');
            $table->decimal('productivity')->default('0');
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
        Schema::dropIfExists('rcp_assigned_agents');
    }
}
