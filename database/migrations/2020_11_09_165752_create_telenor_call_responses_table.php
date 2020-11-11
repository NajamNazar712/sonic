<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelenorCallResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telenor_call_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->bigInteger('tracking_number');
            $table->integer('call_id')->nullable();
            $table->integer('status')->default(0);
            $table->integer('response')->nullable();
            $table->integer('response_status')->nullable();
            $table->integer('error_id')->nullable();
            $table->string('error_code')->nullable();
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
        Schema::dropIfExists('telenor_call_responses');
    }
}
