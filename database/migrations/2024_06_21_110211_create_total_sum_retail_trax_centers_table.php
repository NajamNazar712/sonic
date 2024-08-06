<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTotalSumRetailTraxCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('total_sum_retail_trax_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retail_user_id')->nullable();
            $table->string('trax_center_code')->nullable();
            $table->string('trax_center_name')->nullable();
            $table->integer('sum_of_shipments')->nullable();
            $table->decimal('sum_of_total_charges', 8 ,2)->nullable();
            $table->decimal('sum_of_gst', 8 ,2)->nullable();
            $table->decimal('sum_of_weight_charges', 8 ,2)->nullable();
            $table->decimal('net_commission', 8 ,2)->nullable();
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
        Schema::dropIfExists('total_sum_retail_trax_centers');
    }
}
