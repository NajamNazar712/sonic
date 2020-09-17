<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisionSoftCodPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vision_soft_cod_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_id');
            $table->integer('shipper_id');
            $table->string('city_name');
            $table->decimal('amount');
            $table->decimal('deductable');
            $table->decimal('payable');
            $table->string('company_bank')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('vision_soft_cod_payments');
    }
}
