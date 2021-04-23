<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateDefaultDiscountChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_default_discount_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->integer('added_by');
            $table->timestamp('to')->nullable();
            $table->timestamp('from')->nullable();
            $table->string('weight')->nullable();
            $table->string('title');
            $table->string('cash')->nullable();
            $table->string('insurance')->nullable();
            $table->string('return')->nullable();
            $table->string('packaging')->nullable();
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
        Schema::dropIfExists('corporate_default_discount_charges');
    }
}
