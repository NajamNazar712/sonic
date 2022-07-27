<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisableAccountIntimationQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disable_account_intimation_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('questions');
            $table->string('option1');
            $table->string('option2');
            $table->string('option3');
            $table->string('option4');
            $table->integer('status')->default(1); // status 1 for enable 0 for disable
            $table->integer('created_by')->index()->nullable(); 
            $table->integer('updated_by')->index()->nullable(); 
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
        Schema::dropIfExists('disable_account_intimation_questions');
    }
}
