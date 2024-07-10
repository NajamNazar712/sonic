<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailUserHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_user_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retail_user_id')->index()->nullable();
            $table->integer('trax_center_id')->index()->nullable();
            $table->string('trax_center_name')->nullable();
            $table->string('trax_center_code')->nullable();
            $table->string('joining_date')->nullable();
            $table->string('last_date')->nullable();
            $table->integer('retail_shipping_mode_id')->index()->nullable();
            $table->string('retail_shipping_mode_name')->nullable();
            $table->decimal('product_commission', 8, 2)->nullable();
            $table->string('booking_date')->nullable();
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
        Schema::dropIfExists('retail_user_histories');
    }
}
