<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id')->index();
            $table->integer('sale_person_id')->index()->nullable();
            $table->integer('reference_person_id')->index()->nullable();
            $table->integer('prev_status_id')->index();
            $table->integer('status_id')->index();
            $table->integer('updated_by')->index();
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
        Schema::dropIfExists('lead_logs');
    }
}
