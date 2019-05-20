<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplacementToRegularLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacement_to_regular_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipment_id');
            $table->integer('updated_by');
            $table->integer('replacement_charges');
            $table->integer('product_type_id');
            $table->string('item_description');
            $table->integer('item_quantity');
            $table->integer('item_price');
            $table->tinyInteger('insurance');
            $table->tinyInteger('type');
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
        Schema::dropIfExists('replacement_to_regular_logs');
    }
}
