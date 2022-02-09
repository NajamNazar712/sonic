<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnConfirmationPendingSmsAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_confirmation_pending_sms_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id')->index();
            $table->tinyInteger('status');
            $table->Integer('count');
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
        Schema::dropIfExists('return_confirmation_pending_sms_attempts');
    }
}
