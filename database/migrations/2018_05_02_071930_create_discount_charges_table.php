<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipping_mode_id');
            $table->decimal('weight',8,2)->nullable();
            $table->decimal('cash',8,2)->nullable();
            $table->decimal('insurance',8,2)->nullable();
            $table->decimal('return',8,2)->nullable();
            $table->decimal('packaging',8,2)->nullable();
            $table->timestamp('to');
            $table->timestamp('from');
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
        Schema::dropIfExists('discount_charges');
    }
}
