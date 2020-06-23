<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmEscalationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_escalations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crm_request_status');
            $table->integer('case_nature');
            $table->integer('case_nature_type');
            $table->integer('tat');
            $table->integer('mark_as');
            $table->string('comment');
            $table->integer('updated_by');
            $table->integer('status');
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
        Schema::dropIfExists('crm_escalations');
    }
}
