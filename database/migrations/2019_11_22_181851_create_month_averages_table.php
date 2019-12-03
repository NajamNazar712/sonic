<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthAveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_averages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_id');
            $table->integer('shipments');
            $table->decimal('revenue');
            $table->decimal('avg_revenue');
            $table->decimal('avg_shipments');
            $table->decimal('month_speed');
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
        Schema::dropIfExists('month_averages');
    }
}
