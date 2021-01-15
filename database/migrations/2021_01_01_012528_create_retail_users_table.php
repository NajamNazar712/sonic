<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trax_id')->nullable();
            $table->string('password');
            $table->integer('city_id')->index();
            $table->integer('hub_id')->index();
            $table->string('name');
            $table->string('phone_no');
            $table->string('cnic');
            $table->string('address')->nullable();
            $table->integer('category')->index();
            $table->integer('category_id')->index();
            $table->integer('status')->index();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index()->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('retail_users');
    }
}
