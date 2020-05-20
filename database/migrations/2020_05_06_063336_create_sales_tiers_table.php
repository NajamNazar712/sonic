<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_tiers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tier_name');
            $table->integer('tier_type');
            $table->integer('added_by');
            $table->integer('updated_by')->nullable();
            $table->integer('sales_status');
            $table->double('commission');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('sales_tiers');
    }
}
