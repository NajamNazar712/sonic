<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmAgentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_agent_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('assigned_date');
            $table->integer('admin_id')->index();
            $table->integer('case_nature_id')->index();
            $table->integer('zone_id')->index();
            $table->integer('assinged_requests')->default(0);
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
        Schema::dropIfExists('crm_agent_logs');
    }
}
