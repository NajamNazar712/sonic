<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalRatesStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_rates_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('box_id');
            $table->integer('status')->default(1);
            $table->boolean('cash_handling_charges')->default(0);
            $table->boolean('insurance_charges')->default(0);
            $table->boolean('return_charges')->default(0);
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
        Schema::dropIfExists('international_rates_statuses');
    }
}
