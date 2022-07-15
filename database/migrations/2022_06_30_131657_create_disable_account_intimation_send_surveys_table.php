<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisableAccountIntimationSendSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disable_account_intimation_send_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id')->index();
            $table->string('random_id')->index();
            $table->string('send_by')->index();
            $table->string('send_via');
            $table->string('url');
            $table->string('status')->default(0);
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
        Schema::dropIfExists('disable_account_intimation_send_surveys');
    }
}
