<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmRequestFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_request_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crm_request_id')->index();
            $table->integer('rating_id')->index();
            $table->integer('user_id')->index();
            $table->tinyInteger('reopen')->default(0);
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
        Schema::dropIfExists('crm_request_feedbacks');
    }
}
