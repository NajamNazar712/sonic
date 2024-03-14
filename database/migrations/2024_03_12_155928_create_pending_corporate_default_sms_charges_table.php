<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingCorporateDefaultSmsChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_corporate_default_sms_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->tinyInteger('sms_charges_type_id')->index();
            $table->double('sms_charges');
            $table->boolean('sms_charges_status');
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
        Schema::dropIfExists('pending_corporate_default_sms_charges');
    }
}
