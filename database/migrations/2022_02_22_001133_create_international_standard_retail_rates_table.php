<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalStandardRetailRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_standard_retail_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('range_up', 8,2);
            $table->decimal('range_down', 8,2);
            $table->integer('shipping_mode_id')->index();
            $table->decimal('spkg')->nullable();
            $table->decimal('zone_1', 8,2);
            $table->decimal('zone_2', 8,2);
            $table->decimal('zone_3', 8,2);
            $table->decimal('zone_4', 8,2);
            $table->decimal('zone_5', 8,2);
            $table->decimal('zone_6', 8,2);
            $table->decimal('zone_7', 8,2);
            $table->decimal('zone_8', 8,2);
            $table->decimal('zone_9', 8,2);
            $table->decimal('zone_10', 8,2);
            $table->decimal('zone_11', 8,2);
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
        Schema::dropIfExists('international_standard_retail_rates');
    }
}
