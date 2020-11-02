<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalRatesDiscountChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_rates_discount_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('box_id');
            $table->decimal('weight',8,2)->nullable();
            $table->decimal('cash',8,2)->nullable();
            $table->decimal('insurance',8,2)->nullable();
            $table->decimal('return',8,2)->nullable();
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
        Schema::dropIfExists('international_rates_discount_charges');
    }
}
