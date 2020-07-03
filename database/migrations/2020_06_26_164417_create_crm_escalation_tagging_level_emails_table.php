<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmEscalationTaggingLevelEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_escalation_tagging_level_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('escalation_tagging_id');
            $table->integer('tagging_level_id');
            $table->integer('status');
            $table->string('email');
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
        Schema::dropIfExists('crm_escalation_tagging_level_emails');
    }
}
