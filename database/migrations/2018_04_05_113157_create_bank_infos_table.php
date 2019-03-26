<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bank_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->string('account_no');
            $table->string('account_title');
            $table->string('iban');
            $table->integer('city_code');
            $table->string('payment_mode');
            $table->string('payment_cycle');
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
        Schema::dropIfExists('user_bank_infos');
    }
}
