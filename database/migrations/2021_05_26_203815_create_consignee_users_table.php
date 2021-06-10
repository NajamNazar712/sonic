<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsigneeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignee_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone_number_1');
            $table->string('phone_number_2')->nullable();
            $table->string('address', 255);
            $table->string('pin');
            $table->string('api_token')->nullable();
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
        Schema::dropIfExists('consignee_users');
    }
}
