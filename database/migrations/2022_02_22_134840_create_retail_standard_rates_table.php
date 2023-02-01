<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailStandardRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_standard_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('range_up');
            $table->integer('range_down');
            $table->tinyInteger('weight_addition')->default(0);
            $table->integer('shipping_mode_id')->index();
            $table->integer('trax_box_id')->index()->nullable();
            $table->decimal('kg_range',8,2)->default(0);
            $table->decimal('zone_a', 8, 2)->default(0);
            $table->decimal('zone_b', 8, 2)->default(0);
            $table->decimal('zone_c', 8, 2)->default(0);
            $table->decimal('zone_d', 8, 2)->default(0);
            $table->decimal('within_city', 8, 2)->default(0);
            $table->decimal('same_zone', 8, 2)->default(0);
            $table->decimal('different_zone', 8, 2)->default(0);
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
        Schema::dropIfExists('retail_standard_rates');
    }
}
