<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('case_nature_id');
            $table->integer('case_nature_type_id');
            $table->integer('channel_id');
            $table->integer('status_id');
            $table->integer('launched_by_id')->nullable();
            $table->tinyInteger('launched_by');
            $table->integer('shipment_id')->nullable();
            $table->integer('agent_id');
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
        Schema::dropIfExists('crm_requests');
    }
}
