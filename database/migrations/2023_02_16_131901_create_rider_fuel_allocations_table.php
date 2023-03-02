<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiderFuelAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_fuel_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rider_id')->index();
            $table->integer('hub_id')->index();
            $table->date('date')->index();
            $table->integer('delivery_notes');
            $table->integer('dncc_amount');
            $table->float('fuel_rate')->nullable();
            $table->float('fuel_allocated')->nullable();
            $table->float('amount')->nullable();
            $table->timestamp('allocated_at')->nullable()->index();
            $table->integer('allocated_by')->nullable()->index();
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
        Schema::dropIfExists('rider_fuel_allocations');
    }
}
