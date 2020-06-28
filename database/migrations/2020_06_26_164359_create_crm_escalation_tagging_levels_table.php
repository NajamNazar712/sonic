<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmEscalationTaggingLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_escalation_tagging_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('escalation_tagging_id');
            $table->integer('level_id');
            $table->integer('tagged_id');
            $table->integer('tat');
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
        Schema::dropIfExists('crm_escalation_tagging_levels');
    }
}
