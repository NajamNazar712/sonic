<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraxSpecialHandlingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trax_special_handling_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('handling_code')->nullable();
            $table->string('description')->nullable();
            $table->double('rate')->nullable();
            $table->string('pay_mode')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('trax_special_handling_list');
    }
}
