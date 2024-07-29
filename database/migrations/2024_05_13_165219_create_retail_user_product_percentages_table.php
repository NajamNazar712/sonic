<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailUserProductPercentagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_user_product_percentages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retail_user_id')->index()->nullable();
            $table->integer('retail_shipping_mode_id')->index()->nullable();
            // $table->decimal('gst_percentage', 8,2)->nullable();
            $table->decimal('product_percentage', 8,2)->nullable();
            // $table->decimal('commission_percentage', 8,2)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('retail_user_product_percentages');
    }
}
