<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaeNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kae_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->index();
            $table->integer('shipments');
            $table->decimal('revenue',20,2);
            $table->decimal('avg_revenue',8,2);
            $table->decimal('contribution',8,2);
            $table->bigInteger('target_shipments')->nullable();
            $table->bigInteger('target_revenue')->nullable();
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
        Schema::dropIfExists('kae_numbers');
    }
}
