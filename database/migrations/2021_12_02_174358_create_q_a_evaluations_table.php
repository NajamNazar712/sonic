<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQAEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('q_a_evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id')->index();
            $table->integer('campaign_id')->index();
            $table->integer('evaluated_by')->index();
            $table->string('evaluation_date');
            $table->integer('nature_id')->index();
            $table->string('call_duration')->nullable();
            $table->dateTime('date_time')->nullable();
            $table->integer('query_by')->index();
            $table->string('caller_contact')->nullable();
            $table->string('complain_number')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('score')->default(0);
            $table->string('remarks');
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
        Schema::dropIfExists('q_a_evaluations');
    }
}
