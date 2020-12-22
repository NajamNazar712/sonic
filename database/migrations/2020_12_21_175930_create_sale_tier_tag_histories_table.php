<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleTierTagHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_tier_tag_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_tier_tag_id');
            $table->integer('user_id');
            $table->integer('poc');
            $table->integer('kam');
            $table->integer('ref');
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
        Schema::dropIfExists('sale_tier_tag_histories');
    }
}
