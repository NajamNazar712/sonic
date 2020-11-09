<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryInternationalRatesDiscountChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_international_rates_discount_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('box_id');
            $table->string('weight')->nullable();
            $table->string('cash')->nullable();
            $table->string('insurance')->nullable();
            $table->string('return')->nullable();
            $table->timestamp('to')->nullable();
            $table->timestamp('from')->nullable();
            $table->string('title')->nullable();
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
        Schema::dropIfExists('history_international_rates_discount_charges');
    }
}
