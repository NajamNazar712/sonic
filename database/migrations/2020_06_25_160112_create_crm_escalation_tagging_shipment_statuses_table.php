<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmEscalationTaggingShipmentStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_escalation_tagging_shipment_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('escalation_tagging_id');
            $table->integer('shipment_status_id');
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
        Schema::dropIfExists('crm_escalation_tagging_shipment_statuses');
    }
}
