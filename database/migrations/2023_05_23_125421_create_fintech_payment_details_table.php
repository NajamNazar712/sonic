<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFintechPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fintech_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('trax_pay_id');
            $table->String('transaction_id');
            $table->Integer('cod_amount');
            $table->Integer('rider_tip');
            $table->Integer('rider_id');
            $table->Integer('fintech_company_id');
            $table->Integer('total_fintech_amount');
            $table->Integer('fintech_company_amount');
            $table->Integer('revenue');
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
        Schema::dropIfExists('fintech_payment_details');
    }
}
