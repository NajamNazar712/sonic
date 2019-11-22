<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHubWiseSplitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hub_wise_splits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hub_id');
            $table->integer('shipments');
            $table->decimal('ratio');
            $table->decimal('actual_weight');
            $table->decimal('avg_actual_weight');
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
        Schema::dropIfExists('hub_wise_splits');
    }
}
