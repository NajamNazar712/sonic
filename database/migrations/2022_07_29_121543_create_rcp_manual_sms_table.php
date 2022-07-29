<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRcpManualSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rcp_manual_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_number')->index();
            $table->string('recepient');
            $table->string('recepient_name');
            $table->string('phone');
            $table->string('message');
            $table->string('agent')->index();
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
        Schema::dropIfExists('rcp_manual_sms');
    }
}
