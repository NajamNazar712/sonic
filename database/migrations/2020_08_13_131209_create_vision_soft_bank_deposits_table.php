<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisionSoftBankDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vision_soft_bank_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sdn_id');
            $table->integer('bank_id')->nullable();
            $table->decimal('amount')->nullable();
            $table->decimal('adj_amount')->nullable();
            $table->integer('petty_cash_id')->nullable();
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
        Schema::dropIfExists('vision_soft_bank_deposits');
    }
}
