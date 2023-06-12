<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadCallStatusLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_call_status_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id')->index();
            $table->integer('admin')->nullable()->index();
            $table->string('ip');
            $table->string('lead_status');
            $table->string('call_status');
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
        Schema::dropIfExists('lead_call_status_log');
    }
}
