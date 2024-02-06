<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoltUndeliveredReasonMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolt_undelivered_reason_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reason_id')->index();
            $table->integer('status_attempt_count_1')->index();
            $table->integer('status_attempt_count_2')->index();
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
        Schema::dropIfExists('bolt_undelivered_reason_maps');
    }
}
