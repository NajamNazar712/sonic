<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetailCashDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_cash_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipping_mode_id');
            $table->integer('total_cn');
            $table->integer('category');
            $table->integer('retail_user_id');
            $table->decimal('total_cash', 20,2);
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
        Schema::dropIfExists('retail_cash_deposits');
    }
}
