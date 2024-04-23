<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailFranchiseProductPerecntagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_franchise_product_perecntages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('franchise_id')->nullable()->index();
            $table->integer('product_id')->nullable()->index();
            $table->decimal('product_percentage',8,2)->nullable();
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
        Schema::dropIfExists('retail_franchise_product_perecntages');
    }
}
