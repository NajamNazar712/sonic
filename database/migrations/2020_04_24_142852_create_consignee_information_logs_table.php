<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsigneeInformationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignee_information_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('consignee_information_id');
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->string('phone2')->nullable();
            $table->integer('city_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('consignee_information_logs');
    }
}
