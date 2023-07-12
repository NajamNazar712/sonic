<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRvAssignAgentStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rv_assign_agent_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_status_id')->nullable()->index();
            $table->integer('call_finding_id')->nullable()->index();
            $table->string('name');
            $table->string('shipment_status_name');
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
        Schema::dropIfExists('rv_assign_agent_statuses');
    }
}
