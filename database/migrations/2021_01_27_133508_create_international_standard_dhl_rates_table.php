<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalStandardDhlRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_standard_dhl_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('zone_id')->index();
            $table->decimal('range_up', 8,2);
            $table->decimal('range_down', 8,2);
            $table->tinyInteger('weight_addition')->default(0);
            $table->decimal('spkg')->nullable();
            $table->decimal('charges', 8,2);
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
        Schema::dropIfExists('international_standard_dhl_rates');
    }
}
