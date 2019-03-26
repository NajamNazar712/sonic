<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagingStockHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_stock_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id');
            $table->integer('small_flyers')->nullable();
            $table->integer('medium_flyers')->nullable();
            $table->integer('large_flyers')->nullable();
            $table->integer('boxes')->nullable();
            $table->boolean('entry_type');
            $table->integer('hub_id')->nullable();
            $table->string('reference_number')->nullable();
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
        Schema::dropIfExists('packaging_stock_histories');
    }
}
