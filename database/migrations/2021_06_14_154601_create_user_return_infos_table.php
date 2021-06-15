<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserReturnInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_return_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('return_address', 255);
            $table->string('poc');
            $table->string('phone');
            $table->string('email');
            $table->integer('city_id')->index();
            $table->string('vendor')->nullable();
            $table->tinyInteger('default_address')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('hidden')->default(0);
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
        Schema::dropIfExists('user_return_infos');
    }
}
