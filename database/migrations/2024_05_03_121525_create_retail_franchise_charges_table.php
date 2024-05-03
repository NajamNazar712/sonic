<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailFranchiseChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_franchise_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('franchise_id')->index()->nullable();
            $table->decimal('franchise_gst', 8, 2)->nullable();
            $table->decimal('franchise_withholding', 8, 2)->nullable();
            $table->decimal('franchise_deduction', 8, 2)->nullable();
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
        Schema::dropIfExists('retail_franchise_charges');
    }
}
