<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalEconomyRateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_economy_rate_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('zone_id')->index();
            $table->float('range_up');
            $table->float('range_down');
            $table->boolean('weight_addition');
            $table->float('kg_range');
            $table->float('flat_charges');
            $table->boolean('status')->default(0);  // 0 means new 1 means old
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
        Schema::dropIfExists('international_economy_rate_histories');
    }
}
